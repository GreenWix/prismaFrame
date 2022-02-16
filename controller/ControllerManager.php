<?php

declare(strict_types=1);


namespace GreenWix\prismaFrame\controller;


use GreenWix\prismaFrame\error\internal\InternalError;
use GreenWix\prismaFrame\error\internal\InternalErrorException;
use GreenWix\prismaFrame\error\runtime\RuntimeError;
use GreenWix\prismaFrame\error\runtime\RuntimeErrorException;
use GreenWix\prismaFrame\type\TypeManager;

final class ControllerManager {

	/** @var Controller[] */
	private $controllers = [];

	/** @var ControllerChecker */
	private $checker;

	public function __construct(TypeManager $typeManager){
		$this->checker = new ControllerChecker($typeManager);
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
	 */
	public function addController(Controller $controller){
		$controllerName = $controller->getName();
		if(isset($this->controllers[$controllerName])){
			throw InternalError::ELEMENT_ALREADY_REGISTERED("Controller", $controllerName);
		}

		$controller->methods = $this->checker->getControllerMethods($controller);

		$this->controllers[$controllerName] = $controller;
	}

}