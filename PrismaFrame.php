<?php


namespace SociallHouse\prismaFrame;


use ReflectionException;
use SociallHouse\prismaFrame\controller\Checker;
use SociallHouse\prismaFrame\controller\Controller;
use SociallHouse\prismaFrame\error\Error;
use SociallHouse\prismaFrame\error\HTTPCodes;
use SociallHouse\prismaFrame\error\internal\InternalError;
use SociallHouse\prismaFrame\error\internal\InternalErrorException;
use SociallHouse\prismaFrame\error\runtime\RuntimeError;
use SociallHouse\prismaFrame\error\runtime\RuntimeErrorException;
use SociallHouse\prismaFrame\settings\PrismaFrameSettings;

final class PrismaFrame
{

	private function __construct(){}

	/** @var bool */
	private static $working = false;

	/** @var Controller[] */
	private static $controllers = [];

	/** @var PrismaFrameSettings */
	private static $settings;

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

	public static function start(){
		self::$working = true;
	}

	public static function isWorking(): bool{
		return self::$working;
	}

	/**
	 * @param string $url
	 * @param string $httpMethod
	 * @param array $args
	 * @return Response
	 */
	public static function handle(string $url, string $httpMethod, array $args): Response{
		try {
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

			return new Response(self::getController($controller)->callMethod($method, $httpMethod, $args), HTTPCodes::OK);
		}catch(RuntimeErrorException $e){
			return Error::make($e->id, $e->getMessage(), $e->httpCode);
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
		$controllerName = $controller->getName();
		if(isset(self::$controllers[$controllerName])){
			throw InternalError::ELEMENT_ALREADY_REGISTERED("Контроллер", $controllerName);
		}

		$controller->methods = Checker::getControllerMethods($controller);

		self::$controllers[$controllerName] = $controller;
	}

}