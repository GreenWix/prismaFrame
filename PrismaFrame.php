<?php


namespace GreenWix\prismaFrame;


use Closure;
use GreenWix\prismaFrame\type\SupportedType;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionClass;
use ReflectionException;
use GreenWix\prismaFrame\controller\Checker;
use GreenWix\prismaFrame\controller\Controller;
use GreenWix\prismaFrame\error\Error;
use GreenWix\prismaFrame\error\HTTPCodes;
use GreenWix\prismaFrame\error\internal\InternalError;
use GreenWix\prismaFrame\error\internal\InternalErrorException;
use GreenWix\prismaFrame\error\runtime\RuntimeError;
use GreenWix\prismaFrame\error\runtime\RuntimeErrorException;
use GreenWix\prismaFrame\error\security\SecurityErrorException;
use GreenWix\prismaFrame\settings\PrismaFrameSettings;
use Throwable;

final class PrismaFrame
{

	private function __construct(){}

	/** @var bool */
	private static $working = false;

	/** @var Controller[] */
	private static $controllers = [];

	/** @var PrismaFrameSettings */
	private static $settings;

	private static $security;

	/**
	 * @param PrismaFrameSettings $settings
	 * @throws InternalErrorException
	 * @throws ReflectionException
	 */
	public static function init(PrismaFrameSettings $settings){
		self::$settings = $settings;
		Checker::initSupportedTypes();
	}

	public static function isDebug(): bool{
		return self::$settings->debug;
	}

	/**
	 * @throws InternalErrorException
	 * @throws ReflectionException
	 * @throws RuntimeErrorException
	 */
	public static function start(){
		if(self::$security === null){
			throw InternalError::NO_SECURITY();
		}

		self::validateSecurity();

		self::$working = true;
	}

	/**
	 * @throws ReflectionException
	 * @throws RuntimeErrorException
	 */
	private static function validateSecurity(): void{
		$class = new ReflectionClass(self::$security);

		if(!$class->hasMethod('beforeRequest')){
			throw RuntimeError::BAD_VALIDATION_RESULT('Security класс должен иметь статический метод beforeRequest(ServerRequestInterface)');
		}

		$method = $class->getMethod('beforeRequest');
		if(!$method->isStatic()){
			throw RuntimeError::BAD_VALIDATION_RESULT('Security класс должен иметь СТАТИЧЕСКИЙ метод beforeRequest(ServerRequestInterface) с аргументом типа ' . ServerRequestInterface::class);
		}

		$params = $method->getParameters();
		if(!isset($params[0]) || $params[0]->getType()->getName() !== ServerRequestInterface::class){
			throw RuntimeError::BAD_VALIDATION_RESULT('Security класс должен иметь статический метод beforeRequest(ServerRequestInterface) с аргументом типа ' . ServerRequestInterface::class);
		}

		if(!$class->hasMethod('report')){
			throw RuntimeError::BAD_VALIDATION_RESULT('Security класс должен иметь статический метод report(string)');
		}

		$method = $class->getMethod('report');
		if(!$method->isStatic()){
			throw RuntimeError::BAD_VALIDATION_RESULT('Security класс должен иметь СТАТИЧЕСКИЙ метод report(string) с аргументом типа string');
		}

		$params = $method->getParameters();
		if(!isset($params[0]) || $params[0]->getType()->getName() !== 'string'){
			throw RuntimeError::BAD_VALIDATION_RESULT('Security класс должен иметь статический метод report(string) с аргументом типа string');
		}
	}

	public static function isWorking(): bool{
		return self::$working;
	}

	/**
	 * @param ServerRequestInterface $req
	 * @return Response
	 * @throws InternalErrorException
	 */
	public static function handle(ServerRequestInterface $req): Response{
		if(!self::$working){
			throw InternalError::PRISMAFRAME_IS_NOT_STARTED("PrismaFrame::handle() не может быть выполнен, пока PrismaFrame не запущен (PrismaFrame::start())");
		}

		try {
			$url = $req->getUri()->getPath();
			$httpMethod = strtoupper($req->getMethod());
			$args = $httpMethod === 'GET' ? $req->getQueryParams() : json_decode($req->getBody()->getContents(), true, JSON_THROW_ON_ERROR);

			if (!isset($args["v"])) {
				throw RuntimeError::BAD_INPUT("Parameter \"v\" is required");
			}

			if ($args["v"] !== self::$settings->apiVersion) {
				throw RuntimeError::WRONG_VERSION();
			}

			$raw = explode("/", $url, 2);
			$raw_2 = explode(".", $raw[1] ?? "", 2);

			$controller = $raw_2[0] ?? "";
			$method = $raw_2[1] ?? "";

			self::$security::beforeRequest($req);

			return new Response(self::getController($controller)->callMethod($method, $httpMethod, $args), HTTPCodes::OK);
		}catch(Throwable $e){
			return Error::make($e);
		}
	}

	/**
	 * @param string $name
	 * @return Controller
	 * @throws RuntimeErrorException
	 */
	public static function getController(string $name): Controller{
		if(!isset(self::$controllers[$name])){
			throw RuntimeError::UNKNOWN_CONTROLLER();
		}

		return self::$controllers[$name];
	}

	/**
	 * @param Controller $controller
	 * @throws InternalErrorException
	 * @throws ReflectionException
	 */
	public static function addController(Controller $controller){
		if(self::$working) {
			throw InternalError::PRISMAFRAME_ALREADY_STARTED("PrismaFrame::addController() не может быть выполнен, пока PrismaFrame запущен. Регистрируйте контроллеры до запуска PrismaFrame");
		}

		$controllerName = $controller->getName();
		if(isset(self::$controllers[$controllerName])){
			throw InternalError::ELEMENT_ALREADY_REGISTERED("Контроллер", $controllerName);
		}

		$controller->methods = Checker::getControllerMethods($controller);

		self::$controllers[$controllerName] = $controller;
	}

	/**
	 * @param string $security Класс у Security
	 */
	public static function setSecurity(string $security){
		self::$security = $security;
	}

	/**
	 * @param string $name
	 * @param Closure $validator
	 * @param bool $makeAlsoArrayType
	 * @param string $reasonOnBadValid
	 * @throws InternalErrorException
	 * @throws ReflectionException
	 */
	public static function addSupportedTypeClosure(string $name, Closure $validator, bool $makeAlsoArrayType = false, string $reasonOnBadValid = ''){
		Checker::addSupportedTypeClosure($name, $validator, $makeAlsoArrayType, $reasonOnBadValid);
	}

	/**
	 * @param SupportedType $type
	 */
	public static function addSupportedType(SupportedType $type){
		Checker::addSupportedType($type);
	}

}