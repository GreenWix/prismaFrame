<?php


namespace GreenWix\prismaFrame\controller;


use Closure;
use ReflectionClass;
use ReflectionException;
use ReflectionFunction;
use ReflectionMethod;
use GreenWix\prismaFrame\error\internal\InternalError;
use GreenWix\prismaFrame\error\internal\InternalErrorException;
use GreenWix\prismaFrame\error\runtime\RuntimeError;
use GreenWix\prismaFrame\error\runtime\RuntimeErrorException;

final class Checker
{

	const ARRAY_PARAMETERS = [
		'param'  => true,
		'throws' => true,
	];

	const INTERNAL_CONTROLLER_METHODS = [
		'getName' => true,
		'callMethod' => true
	];

	/** @var array */
	private static $supportedTypes = [];
	private static $supportedTypesCustomErrors = [];

	const ALLOWED_HTTP_METHODS = [
		'GET' => true,
		'POST' => true,
		'PATCH' => true,
		'PUT' => true
	];

	private function __construct(){}

	/**
	 * @throws InternalErrorException
	 * @throws ReflectionException
	 */
	public static function initSupportedTypes(){
		self::addSupportedType('int', static function(string $var, &$readyData, array $extraData): bool{
			if(is_int($var) || preg_match_all('/[^0-9]/', $var) === 0){
				$readyData = intval($var);
				return true;
			}
			return false;
		}, true);

		self::addSupportedType('string', static function(string $var, &$readyData, array $extraData): bool{
			if(is_string($var)) {
				if (isset($extraData[0])) {
					if (!preg_match_all(implode(' ', $extraData[0]), $var)) {
						return false;
					}
				}
				$readyData = $var;
				return true;
			}else return false;
		}, true);

		self::addSupportedType('array', static function(string $var, &$readyData, array $extraData): bool{
			$readyData = explode(',', $var);
			return true;
		});

		self::addSupportedType('bool', static function(string $var, &$readyData, array $extraData): bool{
			switch($var){
				case 'true':
				case '1':
					$readyData = true;
					return true;
				case 'false':
				case '0':
					$readyData = false;
					return true;
				default:
					return false;
			}
		}, true);

		self::addSupportedType('json', static function(string $var, &$readyData, array $extraData): bool{
			// clear json_last_error()
			json_encode(null);

			$readyData = json_decode($var, true);
			return json_last_error() === JSON_ERROR_NONE;
		}, true);

		self::addSupportedType('float', static function(string $var, &$readyData, array $extraData): bool{
			if(is_numeric($var)) {
				$readyData = (float)$var;
				return true;
			}
			return false;
		}, true);
	}

	/**
	 * @param string $name
	 * @param Closure $validator
	 * @param bool $makeAlsoArrayType
	 * @param string $reasonOnBadValid
	 * @throws InternalErrorException
	 * @throws ReflectionException
	 */
	public static function addSupportedType(string $name, Closure $validator, bool $makeAlsoArrayType = false, string $reasonOnBadValid = ""){
		$ref = new ReflectionFunction($validator);
		$returnType = $ref->getReturnType();
		if($returnType === null || $returnType->getName() !== 'bool'){
			throw InternalError::WRONG_RETURN_TYPE('bool', "(Supported type {$name})", 'validator');
		}

		if(isset(self::$supportedTypes[$name])){
			throw InternalError::ELEMENT_ALREADY_REGISTERED('Поддерживаемый тип', $name);
		}

		self::$supportedTypes[$name] = $validator;

		if($makeAlsoArrayType){
			self::addSupportedType($name . '[]', static function(string $var, &$readyData, array $extraData)use($name): bool{
				$readyData = [];
				$part = null;
				foreach(explode(",", $var) as $el){
					if(self::validateSupportedType($name, $el, $part, $extraData)){
						$readyData[] = $part;
					}else return false;
				}
				return true;
			}, false, $reasonOnBadValid);
		}

		if($reasonOnBadValid !== ""){
			self::$supportedTypesCustomErrors[$name] = $reasonOnBadValid;
		}
	}

	/**
	 * @param string $name
	 * @param string $input
	 * @param $var
	 * @param array $extraData
	 * @param string $reason
	 * @return bool
	 * @throws RuntimeErrorException
	 */
	public static function validateSupportedType(string $name, string $input, &$var, array $extraData = [], &$reason = ""): bool{
		if(!isset(self::$supportedTypes[$name])){
			throw RuntimeError::UNKNOWN_PARAMETER_TYPE($name);
		}

		$res = (self::$supportedTypes[$name])($input, $var, $extraData);
		if($res){ return true; }
		$reason = self::$supportedTypesCustomErrors[$name] ?? '';
		return false;
	}

	/**
	 * @param Controller $controller
	 * @return Method[]
	 * @throws ReflectionException
	 * @throws InternalErrorException
	 */
	public static function getControllerMethods(Controller $controller): array{
		$resMethods = [];
		$c = new ReflectionClass($controller);
		$controllerName = $controller->getName();

		$methods = $c->getMethods(ReflectionMethod::IS_PUBLIC);
		foreach ($methods as $method){
			$methodName = $method->getName();
			if(isset(self::INTERNAL_CONTROLLER_METHODS[$methodName])) continue;

			$comment = $method->getDocComment();
			if($comment === false){
				throw InternalError::NO_DOC($controllerName, $methodName);
			}

			$doc = self::parseDoc($comment);

			$returnType = $method->getReturnType();
			/** @noinspection PhpPossiblePolymorphicInvocationInspection */
			if(
				$returnType === null ||
				$returnType->getName() !== 'array' ||
				!isset($doc['return']) ||
				$doc['return'][0] !== 'array'
			){
				throw InternalError::WRONG_RETURN_TYPE('array', $controllerName, $methodName);
			}

			if(!isset($doc['httpMethod'])){
				throw InternalError::NO_SUPPORT_HTTP_METHODS();
			}
			
			$httpMethods = explode('|', strtoupper(implode(" ", $doc['httpMethod'])));
			foreach ($httpMethods as $httpMethod) {
				if (!isset(self::ALLOWED_HTTP_METHODS[$httpMethod])) {
					throw InternalError::WRONG_HTTP_METHOD($controllerName, $methodName, $httpMethod);
				}
			}

			$raw_params = self::getParametersFromDocArray($controllerName, $methodName, $doc);
			$params = [];
			$i = 0;
			foreach ($method->getParameters() as $parameter){
				if (!isset($raw_params[$i])) {
					throw InternalError::NOT_ENOUGH_ARGS($controllerName, $methodName);
				}

				$raw_param = $raw_params[$i];
				if($raw_param->name !== $parameter->getName()){
					throw InternalError::WRONG_ARGS_ORDER($controllerName, $methodName);
				}
				$raw_param->required = !$parameter->isOptional();
				$params[$parameter->getName()] = $raw_param;
				++$i;
			}

			$resMethods[$methodName] = new Method($methodName, $params, $httpMethods, $controller);
		}
		return $resMethods;
	}

	/**
	 * @param string $data
	 * @return array
	 */
	private static function parseDoc(string $data) : array {
		$result = [];
		foreach (explode("\n", $data) as $line){
			$line = trim($line);
			if($line{0} === '*' && $line{1} === ' ' && $line{2} === '@'){
				$raw = explode(' ', $line);
				array_shift($raw); //Избавляемся от '*' в начале
				$param = substr(array_shift($raw), 1);

				if(isset(self::ARRAY_PARAMETERS[$param])){
					$result[$param][] = $raw;
				}else{
					$result[$param] = $raw;
				}
			}
		}
		return $result;
	}

	/**
	 * @param string $controller
	 * @param string $method
	 * @param array $doc
	 * @return MethodParameter[]
	 * @throws InternalErrorException
	 */
	private static function getParametersFromDocArray(string $controller, string $method, array $doc) : array {
		$result = [];
		foreach ($doc['param'] ?? [] as $param){

			if(!isset($param[0], $param[1])) {
				throw InternalError::BAD_DOC('Wrong @param');
			}
			if($param[1]{0} !== "$"){
				throw InternalError::BAD_DOC("@param \"{$param[1]}\" has bad name (without '$')");
			}
			if(isset($result[$param[1]])){
				throw InternalError::BAD_DOC("Duplicate @param \"{$param[1]}\"");
			}
			$types = explode("|", $param[0]);
			$extraData = $types;
			array_shift($extraData);
			foreach ($types as $type){
				if(!isset(self::$supportedTypes[$type])){
					throw InternalError::UNKNOWN_PARAMETER_TYPE($controller, $method, $type);
				}
			}
			$param[1] = substr($param[1], 1);
			$result[] = new MethodParameter($param[1], $types, $extraData, false);
		}
		return $result;
	}

}