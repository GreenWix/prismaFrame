<?php


namespace SociallHouse\prismaFrame\controller;


use SociallHouse\prismaFrame\error\runtime\RuntimeError;
use SociallHouse\prismaFrame\error\runtime\RuntimeErrorException;

abstract class Controller
{

	/** @var Method[] */
	public $methods = [];

	/**
	 * @param string $name
	 * @param array $args
	 * @return array
	 * @throws RuntimeErrorException
	 */
	final public function callMethod(string $name, array $args): array{
		if(!isset($this->methods[$name])){
			throw RuntimeError::UNKNOWN_METHOD();
		}

		return $this->methods[$name]->invoke($args);
	}

	abstract public function getName(): string;

}