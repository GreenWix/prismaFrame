<?php


namespace GreenWix\prismaFrame\error\runtime;


use GreenWix\prismaFrame\error\HTTPCodes;
use GreenWix\prismaFrame\error\PrismaException;

final class RuntimeError
{

	//todo cache exceptions

	private function __construct(){}

	public static function BAD_RESULT(string $message): RuntimeErrorException{
		return new RuntimeErrorException(RuntimeErrorCodes::BAD_RESULT, $message, HTTPCodes::INTERNAL_SERVER_ERROR);
	}

	public static function UNKNOWN_CONTROLLER(): RuntimeErrorException{
		return new RuntimeErrorException(RuntimeErrorCodes::UNKNOWN_CONTROLLER, "Unknown controller", HTTPCodes::NOT_FOUND);
	}

	public static function UNKNOWN_METHOD(string $controllerName, string $methodName): RuntimeErrorException{
		return new RuntimeErrorException(RuntimeErrorCodes::UNKNOWN_METHOD, "Unknown method \"{$controllerName}.{$methodName}\"", HTTPCodes::NOT_FOUND);
	}

	public static function BAD_METHOD_RUN(): RuntimeErrorException{
		return new RuntimeErrorException(RuntimeErrorCodes::BAD_METHOD_RUN, "Can't run controller method", HTTPCodes::INTERNAL_SERVER_ERROR);
	}

	public static function BAD_VALIDATION_RESULT(string $message = "Wrong parameters"): RuntimeErrorException{
		return new RuntimeErrorException(RuntimeErrorCodes::BAD_VALIDATION_RESULT, $message, HTTPCodes::BAD_REQUEST);
	}

	public static function BAD_INPUT(string $message): RuntimeErrorException{
		return new RuntimeErrorException(RuntimeErrorCodes::BAD_INPUT, $message, HTTPCodes::BAD_REQUEST);
	}

	public static function UNKNOWN_PARAMETER_TYPE(string $typeName): RuntimeErrorException{
		return new RuntimeErrorException(RuntimeErrorCodes::UNKNOWN_PARAMETER_TYPE, "Type \"{$typeName}\" is not supported");
	}

	public static function WRONG_HTTP_METHOD(string $supportedHttpMethods): RuntimeErrorException{
		return new RuntimeErrorException(RuntimeErrorCodes::WRONG_HTTP_METHOD, "This method supports only " . $supportedHttpMethods . " HTTP method(s)");
	}

	public static function WRONG_VERSION(): RuntimeErrorException{
		return new RuntimeErrorException(RuntimeErrorCodes::WRONG_VERSION, "This version is incompatible");
	}

}