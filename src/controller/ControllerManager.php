<?php

declare(strict_types=1);


namespace GreenWix\prismaFrame\controller;


use GreenWix\prismaFrame\controller\exception\UnknownControllerException;
use GreenWix\prismaFrame\error\InternalErrorException;
use GreenWix\prismaFrame\PrismaFrame;

final class ControllerManager {

	/** @var Controller[] */
	private $controllers = [];

	/** @var ControllerChecker */
	private $checker;

	public function __construct(PrismaFrame $prismaFrame) {
		$this->checker = new ControllerChecker($prismaFrame);
	}

	/**
	 * @param string $name
	 * @return Controller
	 * @throws UnknownControllerException
	 */
	public function getController(string $name): Controller {
		if (!isset($this->controllers[$name])) {
			throw new UnknownControllerException();
		}

		return $this->controllers[$name];
	}

	/**
	 * @param Controller $controller
	 * @throws InternalErrorException
	 */
	public function addController(Controller $controller) {
		$controllerName = $controller->getName();
		if (isset($this->controllers[$controllerName])) {
			throw new InternalErrorException("Controller $controllerName is already registered");
		}

		$controller->methods = $this->checker->getControllerMethods($controller);

		$this->controllers[$controllerName] = $controller;
	}

}