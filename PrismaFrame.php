<?php


namespace SociallHouse\prismaFrame;


use ReflectionException;
use SociallHouse\prismaFrame\controller\Checker;
use SociallHouse\prismaFrame\controller\Controller;
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
	 * @param string $name
	 * @return Controller
	 * @throws RuntimeErrorException
	 */
	public function getController(string $name): Controller{
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
	public function addController(Controller $controller){
		$controllerName = $controller->getName();
		if(isset(self::$controllers[$controllerName])){
			throw InternalError::ELEMENT_ALREADY_REGISTERED("Контроллер", $controllerName);
		}

		$controller->methods = Checker::getControllerMethods($controller);

		self::$controllers[$controllerName] = $controller;
	}

}