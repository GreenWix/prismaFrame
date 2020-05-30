<?php


namespace SociallHouse\prismaFrame\controller;


use Closure;
use ReflectionClass;
use ReflectionException;
use ReflectionFunction;
use ReflectionMethod;
use SociallHouse\prismaFrame\error\internal\InternalError;
use SociallHouse\prismaFrame\error\internal\InternalErrorException;
use SociallHouse\prismaFrame\error\runtime\RuntimeError;
use SociallHouse\prismaFrame\error\runtime\RuntimeErrorException;

final class Checker
{

	const ARRAY_PARAMETERS = [
		'param'  => true,
		'throws' => true,
	];

	/** @var array */
	private static $supportedTypes = [];
	private static $supportedTypesCustomErrors = [];

	const ALLOWED_HTTP_METHODS = [
		"get", "post", "patch", "put"
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
					if (!preg_match_all(implode(" ", $extraData[0]), $var)) {
						return false;
					}
				}
				$readyData = $var;
				return true;
			}else return false;
		}, true);

		self::addSupportedType('array', static function(string $var, &$readyData, array $extraData): bool{
			$readyData = explode(",", $var);
			return true;
		});

		self::addSupportedType('bool', static function(string $var, &$readyData, array $extraData): bool{
			switch($var){
				case "true":
				case "1":
					$readyData = true;
					return true;
				case "false":
				case "0":
					$readyData = false;
					return true;
				default:
					return false;
			}
		}, true);

		self::addSupportedType('json', static function(string $var, &$readyData, array $extraData): bool{
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
		if($ref->getReturnType()->getName() !== "bool"){
			throw InternalError::WRONG_RETURN_TYPE("bool");
		}

		if(isset(self::$supportedTypes[$name])){
			throw InternalError::ELEMENT_ALREADY_REGISTERED("Поддерживаемый тип", $name);
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
			throw RuntimeError::UNKNOWN_PARAMETER_TYPE($name); // да-да, во время работы могут быть Internal ошибки, но только в самых критических моментах, хотя этот кейс в теории невозможен, но на всякий случай проверка стоит
		}

		$res = (self::$supportedTypes[$name])($input, $var, $extraData);
		if($res){ return true; }
		$reason = self::$supportedTypesCustomErrors[$name] ?? "";
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
		$methods = $c->getMethods(ReflectionMethod::IS_PUBLIC);
		foreach ($methods as $method){
			$comment = $method->getDocComment();
			if($comment === false){
				throw InternalError::NO_DOC();
			}

			$doc = self::parseDoc($comment);

			if(
				$method->getReturnType()->getName() !== 'array' ||
				!isset($doc['return']) ||
				$doc['return'][0] !== 'array'
			){
				throw InternalError::WRONG_RETURN_TYPE();
			}

			if(!isset($doc['method'])){
				throw InternalError::NO_SUPPORT_HTTP_METHODS();
			}
			
			$httpMethods = explode("|", strtolower(implode(" ", $doc['method'])));
			foreach ($httpMethods as $httpMethod) {
				if (!in_array($httpMethod, self::ALLOWED_HTTP_METHODS)) {
					throw InternalError::NO_SUPPORT_HTTP_METHODS();
				}
			}

			$params = self::getParametersFromDocArray($doc);
			foreach ($method->getParameters() as $parameter){
				if (!isset($params[$parameter->name])) {
					throw InternalError::BAD_DOC("Controller's doc hasn't all parameters");
				}
				$params[$parameter->name]->required = !$parameter->isOptional();
			}

			$resMethods[] = new Method($method->getName(), $params, $httpMethods, $controller);
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
	 * @param array $doc
	 * @return MethodParameter[]
	 * @throws InternalErrorException
	 */
	private static function getParametersFromDocArray(array $doc) : array {
		$result = [];
		foreach ($doc['param'] ?? [] as $param){

			if(!isset($param[0], $param[1])) {
				throw InternalError::BAD_DOC("Wrong @param");
			}
			if($param[1]{0} !== "$"){
				throw InternalError::BAD_DOC("@param \"" . $param[1] . "\" has bad name (without '$')");
			}
			if(isset($result[$param[1]])){
				throw InternalError::BAD_DOC("Duplicate @param \"" . $param[1] . "\"");
			}
			$types = explode("|", $param[0]);
			$extraData = $types;
			array_shift($extraData);
			foreach ($types as $type){
				if(!isset(self::$supportedTypes[$type])){
					throw InternalError::UNKNOWN_PARAMETER_TYPE($type);
				}
			}
			$param[1] = substr($param[1], 1);
			$result[$param[1]] = new MethodParameter($param[1], $types, $extraData, false);
		}
		return $result;
	}

}