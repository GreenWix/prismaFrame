<?php


namespace GreenWix\prismaFrame\controller;


use GreenWix\prismaFrame\error\runtime\RuntimeError;
use GreenWix\prismaFrame\error\runtime\RuntimeErrorException;

final class Method
{

	/** @var string */
	public $name;

	/** @var MethodParameter[] */
	private $parameters;

	/** @var array */
	private $httpMethods = [];

	// Используется для вывода об ошибке, которая появляется если запрос сделан с неподдерживаемым HTTP методом
	// Нужен для того, чтобы постоянно implode("|", httpMethods) не делать
	/** @var string */
	private $flatHttpMethods;

	/** @var Controller */
	private $controller;

	/**
	 * Method constructor.
	 * @param string $name
	 * @param array $parameters
	 * @param array $httpMethods
	 * @param Controller $controller
	 */
	public function __construct(string $name, array $parameters, array $httpMethods, Controller $controller){
		$this->name = $name;
		$this->parameters = $parameters;
		foreach ($httpMethods as $method){
			$this->httpMethods[$method] = true; // чтобы можно было потом ускоренно проверять через isset
		}
		$this->flatHttpMethods = implode("|", $httpMethods);
		$this->controller = $controller;
	}

	/**
	 * @param string $httpMethod
	 * @param array $args
	 * @return array
	 * @throws RuntimeErrorException
	 */
	public function invoke(string $httpMethod, array $args): array{
		if(!isset($this->httpMethods[strtoupper($httpMethod)])){
			throw RuntimeError::WRONG_HTTP_METHOD($httpMethod, $this->flatHttpMethods);
		}

		$values = [];
		foreach ($this->parameters as $name => $param){
			if($param->required && !isset($args[$name])){
				throw RuntimeError::BAD_INPUT("Parameter \"{$name}\" is required");
			}
			if(isset($args[$name])) {
				if ($param->validate($args[$name], $result, $reason)) {
					$values[] = $result;
				} else {
					throw RuntimeError::BAD_VALIDATION_RESULT($reason === "" ? "Parameter \"{$name}\" accepts only " . $param->flatTypes . " types" : $reason);
				}
			}
		}

		return $this->controller->{$this->name}(...$values);
	}

}