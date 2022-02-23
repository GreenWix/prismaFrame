<?php


namespace GreenWix\prismaFrame\controller;


use GreenWix\prismaFrame\controller\exception\UnknownMethodException;
use GreenWix\prismaFrame\type\TypeManagerException;

abstract class Controller {

	/** @var Method[] */
	public $methods = [];


	/**
	 * @throws exception\WrongHttpMethodException
	 * @throws exception\BadInputException
	 * @throws UnknownMethodException
	 * @throws TypeManagerException
	 */
	final public function callMethod(string $methodName, string $httpMethod, array $args): array {
		$this->checkIfMethodExists($methodName);

		return $this->methods[$methodName]->invoke($httpMethod, $args);
	}

	/**
	 * @throws UnknownMethodException
	 */
	private function checkIfMethodExists(string $methodName): void {
		if (isset($this->methods[$methodName])) {
			return;
		}

		$controllerName = $this->getName();
		$controllerAndMethodName = "$controllerName.$methodName";

		throw new UnknownMethodException("Unknown method \"$controllerAndMethodName\"");
	}

	abstract public function getName(): string;

}