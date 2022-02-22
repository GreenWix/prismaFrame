<?php


namespace GreenWix\prismaFrame\controller;


use GreenWix\prismaFrame\error\runtime\RuntimeError;
use GreenWix\prismaFrame\error\runtime\RuntimeErrorException;
use GreenWix\prismaFrame\type\TypeManagerException;

abstract class Controller
{

	/** @var Method[] */
	public $methods = [];

	/**
	 * @param string $name
	 * @param string $httpMethod
	 * @param array $args
	 * @return array
	 * @throws RuntimeErrorException
	 * @throws TypeManagerException
	 */
	final public function callMethod(string $name, string $httpMethod, array $args): array{
		if(!isset($this->methods[$name])){
			throw RuntimeError::UNKNOWN_METHOD($this->getName(), $name);
		}

		return $this->methods[$name]->invoke($httpMethod, $args);
	}

	abstract public function getName(): string;

}