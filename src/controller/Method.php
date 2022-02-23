<?php


namespace GreenWix\prismaFrame\controller;


use GreenWix\prismaFrame\controller\exception\BadInputException;
use GreenWix\prismaFrame\controller\exception\WrongHttpMethodException;
use GreenWix\prismaFrame\error\runtime\RuntimeError;
use GreenWix\prismaFrame\error\runtime\RuntimeErrorException;
use GreenWix\prismaFrame\type\TypeManagerException;

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
	 * @throws BadInputException
	 * @throws TypeManagerException
	 * @throws WrongHttpMethodException
	 */
	public function invoke(string $httpMethod, array $args): array{
		if(!isset($this->httpMethods[strtoupper($httpMethod)])){
			throw new WrongHttpMethodException("This method supports only $this->flatHttpMethods HTTP method(s). Got $httpMethod");
		}

		$values = [];
		foreach ($this->parameters as $name => $param){
			if($param->required && !isset($args[$name])){
				throw new BadInputException("Parameter \"$name\" is required");
			}

			if(!isset($args[$name])) {
				continue;
			}

			$argValue = $args[$name];
			$values[] = $param->validateAndGetValue($argValue);
		}

		return $this->controller->{$this->name}(...$values);
	}

}