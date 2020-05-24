<?php


namespace SociallHouse\prismaFrame\error\runtime;


use SociallHouse\prismaFrame\error\HTTPCodes;
use SociallHouse\prismaFrame\error\PrismaException;

final class RuntimeError
{

	private function __construct(){}

	public static function BAD_RESULT(string $message): RuntimeErrorException{
		return new RuntimeErrorException(RuntimeErrorCodes::BAD_RESULT, $message, HTTPCodes::INTERNAL_SERVER_ERROR);
	}

	public static function UNKNOWN_CONTROLLER(): RuntimeErrorException{
		return new RuntimeErrorException(RuntimeErrorCodes::UNKNOWN_CONTROLLER, "Unknown controller", HTTPCodes::NOT_FOUND);
	}

	public static function UNKNOWN_METHOD(): RuntimeErrorException{
		return new RuntimeErrorException(RuntimeErrorCodes::UNKNOWN_METHOD, "Unknown method", HTTPCodes::NOT_FOUND);
	}

	public static function BAD_METHOD_RUN(): RuntimeErrorException{
		return new RuntimeErrorException(RuntimeErrorCodes::BAD_METHOD_RUN, "Can't run controller method", HTTPCodes::INTERNAL_SERVER_ERROR);
	}

	public static function BAD_VALIDATION_RESULT(string $message = "Wrong parameters"): RuntimeErrorException{
		return new RuntimeErrorException(RuntimeErrorCodes::BAD_VALIDATION_RESULT, $message, HTTPCodes::BAD_REQUEST);
	}

}