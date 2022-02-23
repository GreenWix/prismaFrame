<?php


namespace GreenWix\prismaFrame\controller;


use GreenWix\prismaFrame\error\HTTPCodes;
use GreenWix\prismaFrame\error\internal\InternalErrorException;
use GreenWix\prismaFrame\PrismaFrame;
use ReflectionClass;
use ReflectionMethod;
use Throwable;

class ControllerChecker {

	/** @var PrismaFrame */
	protected $prismaFrame;

	public function __construct(PrismaFrame $prismaFrame) {
		$this->prismaFrame = $prismaFrame;
	}

	/**
	 * @param Controller $controller
	 * @return Method[]
	 * @throws InternalErrorException
	 */
	public function getControllerMethods(Controller $controller): array {
		$resultMethods = [];
		$controllerClass = new ReflectionClass($controller);
		$controllerName = $controller->getName();

		$methods = $controllerClass->getMethods(ReflectionMethod::IS_PUBLIC);
		foreach ($methods as $method) {
			$methodName = $method->getName();
			if ($this->isMethodInternal($methodName)) continue;

			$controllerAndMethodName = "$controllerName.$methodName";

			try {
				$resultMethods[$methodName] = $this->checkAndGetMethod($method, $controller);
			} catch (Throwable $e) {
				throw new InternalErrorException("При обработке метода $controllerAndMethodName произошла ошибка", HTTPCodes::INTERNAL_SERVER_ERROR, $e);
			}
		}
		return $resultMethods;
	}

	/**
	 * @param ReflectionMethod $method
	 * @param Controller $controller
	 * @return Method
	 * @throws InternalErrorException
	 * @throws InternalErrorException
	 */
	protected function checkAndGetMethod(ReflectionMethod $method, Controller $controller): Method {
		$methodName = $method->getName();
		$controllerName = $controller->getName();

		$comment = $method->getDocComment();
		if ($comment === false) {
			throw new InternalErrorException("Метод не содержит php-doc");
		}

		$doc = self::parseDoc($comment);

		$this->checkReturnType($method, $doc);

		if (!isset($doc['httpMethod'])) {
			throw new InternalErrorException('Php-doc метода должен содержать @httpMethod <GET|POST|PATCH|PUT или несколько методов перечисленных через "|">');
		}

		$httpMethods = $this->getHttpMethods($doc);
		foreach ($httpMethods as $httpMethod) {
			if (!$this->isHttpMethodAllowed($httpMethod)) {
				throw new InternalErrorException("HTTP метод \"$httpMethod\" не поддерживается");
			}
		}

		$parameters = $this->checkAndGetParameters($method, $doc);

		return new Method($methodName, $parameters, $httpMethods, $controller);
	}

	/**
	 * @param ReflectionMethod $method
	 * @param array $doc
	 * @return array
	 * @throws InternalErrorException
	 * @throws InternalErrorException
	 */
	protected function checkAndGetParameters(ReflectionMethod $method, array $doc): array {
		$docParameters = $this->getParametersFromDocArray($doc);
		$resultParameters = [];

		$i = 0;
		foreach ($method->getParameters() as $methodParameter) {
			if (!isset($docParameters[$i])) {
				throw new InternalErrorException("Php-doc содержит упоминание не всех аргументов функции");
			}

			$docParameter = $docParameters[$i];
			$parameterName = $methodParameter->getName();
			if ($docParameter->name !== $parameterName) {
				throw new InternalErrorException("Порядок аргументов в php-doc не совпадает с порядком аргументов функции");
			}

			$docParameter->required = !$methodParameter->isOptional();
			$resultParameters[$parameterName] = $docParameter;

			$this->checkParameterType($docParameter);

			++$i;
		}

		return $resultParameters;
	}

	/**
	 * @throws InternalErrorException
	 */
	protected function checkParameterType(MethodParameter $docParameter): void {
		$parameterTypeName = $docParameter->typeName;
		$parameterName = $docParameter->name;

		$typeManager = $this->prismaFrame->getTypeManager();

		if (!$typeManager->hasTypeValidator($parameterTypeName)) {
			throw new InternalErrorException("Тип $parameterTypeName (аргумент $parameterName) не является поддерживаемым");
		}
	}

	protected function getHttpMethods(array $doc): array {
		$methods = implode(" ", $doc['httpMethod']);
		$uppercaseMethods = strtoupper($methods);

		return explode('|', $uppercaseMethods);
	}

	/**
	 * @param ReflectionMethod $method
	 * @param array $doc
	 * @throws InternalErrorException
	 */
	protected function checkReturnType(ReflectionMethod $method, array $doc): void {
		$returnType = $method->getReturnType();

		$requiredReturnType = 'array';

		if($returnType === null){
			throw new InternalErrorException("Метод не возвращает ничего, хотя должен $requiredReturnType");
		}

		if($returnType->getName() !== $requiredReturnType){
			throw new InternalErrorException("Метод возвращает иной тип, хотя должен $requiredReturnType");
		}

		if(!isset($doc['return'])){
			throw new InternalErrorException("В PhpDoc метода не прописан @return");
		}

		$docReturnType = $doc['return'][0];
		if($docReturnType !== $requiredReturnType) {
			throw new InternalErrorException("В PhpDoc метода @return указывает на иной тип, хотя должен $requiredReturnType");
		}
	}

	protected function parseDoc(string $data): array {
		$result = [];
		$lines = explode("\n", $data);

		foreach ($lines as $line) {
			$line = trim($line);

			/* 
			 * проверяется сценарий такой же как в этом комментарии
			 * @parameter value
			 */
			$parameterPrefix = "* @"; // первые 3 символа строки с параметром
			$isLineWithParameter = substr($line, 0, 3) === $parameterPrefix;

			if (!$isLineWithParameter) {
				continue;
			}

			$tokens = explode(' ', $line);
			array_shift($tokens); //Избавляемся от '*' в начале

			$parameterNameWithAmpersat = array_shift($tokens); // ampersat - @
			$parameterName = substr($parameterNameWithAmpersat, 1);

			if ($this->isArrayParameter($parameterName)) {
				$result[$parameterName][] = $tokens;
			} else {
				$result[$parameterName] = $tokens;
			}
		}

		return $result;
	}

	/**
	 * @param array $doc
	 * @return MethodParameter[]
	 * @throws InternalErrorException
	 */
	private function getParametersFromDocArray(array $doc): array {
		$result = [];

		foreach ($doc['param'] ?? [] as $param) {
			/* просто напомню как в доке это лежит
			 * @param type $var some extra data
			 *
			 * соответственно в $param будет
			 * ["type", "$var", "some", "extra", "data"]
			 */

			if (!isset($param[0], $param[1])) {
				throw new InternalErrorException('Wrong @param line');
			}

			$typeName = array_shift($param);
			$parameterName = array_shift($param);

			if ($parameterName[0] !== "$") {
				throw new InternalErrorException("@param \"{$parameterName}\" has bad name (without '$')");
			}

			if (isset($result[$parameterName])) {
				throw new InternalErrorException("Duplicate @param \"{$parameterName}\" line");
			}

			$extraData = $param;

			$parameterName = substr($parameterName, 1); // убираем $

			$result[] = new MethodParameter($this->prismaFrame->getTypeManager(), $parameterName, $typeName, $extraData, false);
		}

		return $result;
	}


	public function isArrayParameter(string $parameterName): bool {
		$arrayParameters = ['param', 'throws'];

		return in_array($parameterName, $arrayParameters, true);
	}

	public function isMethodInternal(string $methodName): bool {
		$internalMethods = ['getName', 'callMethod'];

		return in_array($methodName, $internalMethods, true);
	}

	public function isHttpMethodAllowed(string $httpMethod): bool {
		$allowedHttpMethods = ['GET', 'POST', 'PATCH', 'PUT'];

		return in_array($httpMethod, $allowedHttpMethods, true);
	}

}