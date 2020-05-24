<?php


namespace SociallHouse\prismaFrame\error\runtime;


use SociallHouse\prismaFrame\error\HTTPCodes;
use SociallHouse\prismaFrame\error\PrismaException;

final class RuntimeError
{

	private function __construct(){}

	public static function BAD_RESULT(string $message): PrismaException{
		return new PrismaException(RuntimeErrorCodes::BAD_RESULT, $message, HTTPCodes::INTERNAL_SERVER_ERROR);
	}

	public static function UNKNOWN_CONTROLLER(): PrismaException{
		return new PrismaException(RuntimeErrorCodes::UNKNOWN_CONTROLLER, "Unknown controller", HTTPCodes::NOT_FOUND);
	}

	public static function UNKNOWN_METHOD(): PrismaException{
		return new PrismaException(RuntimeErrorCodes::UNKNOWN_METHOD, "Unknown method", HTTPCodes::NOT_FOUND);
	}

	public static function BAD_METHOD_RUN(): PrismaException{
		return new PrismaException(RuntimeErrorCodes::BAD_METHOD_RUN, "Can't run controller method", HTTPCodes::INTERNAL_SERVER_ERROR);
	}

	public static function BAD_VALIDATION_RESULT(string $message = "Wrong parameters"): PrismaException{
		return new PrismaException(RuntimeErrorCodes::BAD_VALIDATION_RESULT, $message, HTTPCodes::BAD_REQUEST);
	}

}