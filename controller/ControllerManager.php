<?php

declare(strict_types=1);


namespace GreenWix\prismaFrame\controller;


use GreenWix\prismaFrame\error\internal\InternalError;
use GreenWix\prismaFrame\error\internal\InternalErrorException;
use GreenWix\prismaFrame\error\runtime\RuntimeError;
use GreenWix\prismaFrame\error\runtime\RuntimeErrorException;
use GreenWix\prismaFrame\PrismaFrame;
use ReflectionException;

final class ControllerManager {

	/** @var Controller[] */
	private $controllers = [];

	/** @var PrismaFrame */
	private $prismaFrame;

	/** @var ControllerChecker */
	private $checker;

	public function __construct(PrismaFrame $prismaFrame){
		$this->prismaFrame = $prismaFrame;
		$this->checker = new ControllerChecker($prismaFrame);
	}

	/**
	 * @param string $name
	 * @return Controller
	 * @throws RuntimeErrorException
	 */
	public function getController(string $name): Controller{
		if(!isset($this->controllers[$name])){
			throw RuntimeError::UNKNOWN_CONTROLLER();
		}

		return $this->controllers[$name];
	}

	/**
	 * @param Controller $controller
	 * @throws InternalErrorException
	 * @throws ReflectionException
	 */
	public function addController(Controller $controller){
		if($this->prismaFrame->isWorking()) {
			throw InternalError::PRISMAFRAME_ALREADY_STARTED("You cant add new controllers while prismaFrame is working");
		}

		$controllerName = $controller->getName();
		if(isset($this->controllers[$controllerName])){
			throw InternalError::ELEMENT_ALREADY_REGISTERED("Controller", $controllerName);
		}

		$controller->methods = $this->checker->getControllerMethods($controller);

		$this->controllers[$controllerName] = $controller;
	}

}