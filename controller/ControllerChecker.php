<?php


namespace GreenWix\prismaFrame\controller;


use Closure;
use GreenWix\prismaFrame\PrismaFrame;
use GreenWix\prismaFrame\type\TypedArrayTypeValidator;
use GreenWix\prismaFrame\type\TypeManager;
use GreenWix\prismaFrame\type\TypeValidator;
use ReflectionClass;
use ReflectionException;
use ReflectionFunction;
use ReflectionMethod;
use GreenWix\prismaFrame\error\internal\InternalError;
use GreenWix\prismaFrame\error\internal\InternalErrorException;
use GreenWix\prismaFrame\error\runtime\RuntimeError;
use GreenWix\prismaFrame\error\runtime\RuntimeErrorException;
use ReflectionParameter;
use Throwable;

class ControllerChecker
{

	/** @var PrismaFrame */
	protected $prismaFrame;

	public function __construct(PrismaFrame $prismaFrame){
		$this->prismaFrame = $prismaFrame;
	}

	/**
	 * @param Controller $controller
	 * @return Method[]
	 * @throws InternalErrorException
	 */
	public function getControllerMethods(Controller $controller): array{
		$resultMethods = [];
		$controllerClass = new ReflectionClass($controller);

		$methods = $controllerClass->getMethods(ReflectionMethod::IS_PUBLIC);
		foreach ($methods as $method){
			$methodName = $method->getName();
			if($this->isMethodInternal($methodName)) continue;

			$resultMethods[$methodName] = $this->checkAndGetMethod($method, $controller);
		}
		return $resultMethods;
	}

	/**
	 * @param ReflectionMethod $method
	 * @param Controller $controller
	 * @return Method
	 * @throws InternalErrorException
	 */
	protected function checkAndGetMethod(ReflectionMethod $method, Controller $controller): Method{
		$methodName = $method->getName();
		$controllerName = $controller->getName();

		$comment = $method->getDocComment();
		if($comment === false){
			throw InternalError::NO_DOC($controllerName, $methodName);
		}

		$doc = self::parseDoc($comment);

		$this->checkReturnType($method, $controllerName, $doc);

		if(!isset($doc['httpMethod'])){
			throw InternalError::NO_SUPPORT_HTTP_METHODS();
		}

		$httpMethods = $this->getHttpMethods($doc);
		foreach ($httpMethods as $httpMethod) {
			if (!$this->isHttpMethodAllowed($httpMethod)) {
				throw InternalError::WRONG_HTTP_METHOD($controllerName, $methodName, $httpMethod);
			}
		}

		$parameters = $this->checkAndGetParameters($controllerName, $methodName, $method, $doc);

		return new Method($methodName, $parameters, $httpMethods, $controller);
	}

	/**
	 * @param string $controllerName
	 * @param string $methodName
	 * @param ReflectionMethod $method
	 * @param array $doc
	 * @return array
	 * @throws InternalErrorException
	 */
	protected function checkAndGetParameters(string $controllerName, string $methodName, ReflectionMethod $method, array $doc): array{
		$docParameters = $this->getParametersFromDocArray($doc);
		$resultParameters = [];

		$i = 0;
		foreach ($method->getParameters() as $methodParameter){
			if (!isset($docParameters[$i])) {
				throw InternalError::NOT_ENOUGH_ARGS($controllerName, $methodName);
			}

			$docParameter = $docParameters[$i];
			if($docParameter->name !== $methodParameter->getName()){
				throw InternalError::WRONG_ARGS_ORDER($controllerName, $methodName);
			}

			$docParameter->required = !$methodParameter->isOptional();

			$resultParameters[$methodParameter->getName()] = $docParameter;

			$this->checkParameterType($controllerName, $methodName, $methodParameter, $docParameter);

			++$i;
		}

		return $resultParameters;
	}

	// todo отказаться от этой ереси с протаскиванием controllerName и methodName по всем методам и просто нормально использовать эксепшены

	/**
	 * @throws InternalErrorException
	 */
	protected function checkParameterType(string $controllerName, string $methodName, ReflectionParameter $methodParameter, MethodParameter $docParameter): void{
		$methodParameterTypeName = $methodParameter->getType()->getName();
		$docParameterTypeName = $docParameter->typeName;

		$typeManager = $this->prismaFrame->getTypeManager();

		if(!$typeManager->hasTypeValidator($methodParameterTypeName)){
			throw InternalError::UNKNOWN_PARAMETER_TYPE($controllerName, $methodName, $methodParameterTypeName);
		}
	}

	protected function getHttpMethods(array $doc): array{
		$methods = implode(" ", $doc['httpMethod']);
		$uppercaseMethods = strtoupper($methods);

		return explode('|', $uppercaseMethods);
	}

	/**
	 * @param ReflectionMethod $method
	 * @param string $controllerName
	 * @param array $doc
	 * @throws InternalErrorException
	 */
	protected function checkReturnType(ReflectionMethod $method, string $controllerName, array $doc): void{
		$returnType = $method->getReturnType();

		if(
			$returnType === null ||
			$returnType->getName() !== 'array' ||
			!isset($doc['return']) ||
			$doc['return'][0] !== 'array'
		){
			throw InternalError::WRONG_RETURN_TYPE('array', $returnType, $doc, $controllerName, $method->getName());
		}
	}

	protected function parseDoc(string $data): array {
		$result = [];
		$lines = explode("\n", $data);
		
		foreach ($lines as $line){
			$line = trim($line);
			
			/* 
			 * проверяется сценарий такой же как в этом комментарии
			 * @parameter value
			 */
			$parameterPrefix = "* @"; // первые 3 символа строки с параметром
			$isLineWithParameter = substr($line, 0, 3) === $parameterPrefix;
			
			if(!$isLineWithParameter) {
				continue;
			}
			
			$tokens = explode(' ', $line);
			array_shift($tokens); //Избавляемся от '*' в начале

			$parameterNameWithAmpersat = array_shift($tokens); // ampersat - @
			$parameterName = substr($parameterNameWithAmpersat, 1);

			if($this->isArrayParameter($parameterName)){
				$result[$parameterName][] = $tokens;
			}else{
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

		foreach ($doc['param'] ?? [] as $param){
			/* просто напомню как в доке это лежит
			 * @param type $var some extra data
			 *
			 * соответственно в $param будет
			 * ["type", "$var", "some", "extra", "data"]
			 */

			if(!isset($param[0], $param[1])) {
				throw InternalError::BAD_DOC('Wrong @param');
			}

			$typeName = array_shift($param);
			$parameterName = array_shift($param);

			if($parameterName[0] !== "$"){
				throw InternalError::BAD_DOC("@param \"{$parameterName}\" has bad name (without '$')");
			}

			if(isset($result[$parameterName])){
				throw InternalError::BAD_DOC("Duplicate @param \"{$parameterName}\"");
			}

			$extraData = $param;

			$parameterName = substr($parameterName, 1); // убираем $

			$result[] = new MethodParameter($this->prismaFrame->getTypeManager(), $parameterName, $typeName, $extraData, false);
		}

		return $result;
	}



	public function isArrayParameter(string $parameterName): bool{
		$arrayParameters = ['param', 'throws'];

		return in_array($parameterName, $arrayParameters, true);
	}

	public function isMethodInternal(string $methodName): bool{
		$internalMethods = ['getName', 'callMethod'];

		return in_array($methodName, $internalMethods, true);
	}

	public function isHttpMethodAllowed(string $httpMethod): bool{
		$allowedHttpMethods = ['GET', 'POST', 'PATCH', 'PUT'];

		return in_array($httpMethod, $allowedHttpMethods, true);
	}

}