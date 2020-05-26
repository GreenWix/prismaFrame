<?php


namespace SociallHouse\prismaFrame\error\internal;


final class InternalError
{

	/*
	 * Internal ошибки проявляются только при запуске процесса, когда происходит валидация контроллеров и их методов
	 * Во время работы процесса данные ошибки не появляются
	 */

	private function __construct(){}

	public static function NO_DOC(): InternalErrorException{
		return new InternalErrorException(InternalErrorCodes::NO_DOC, "Метод не содержит php-doc");
	}

	public static function WRONG_RETURN_TYPE(): InternalErrorException{
		return new InternalErrorException(InternalErrorCodes::WRONG_RETURN_TYPE, "Метод должен возвращать тип array");
	}

	public static function NO_SUPPORT_HTTP_METHODS(): InternalErrorException{
		return new InternalErrorException(InternalErrorCodes::NO_SUPPORT_HTTP_METHODS, "Php-doc метода должен содержать @method <GET|POST|PATCH|PUT>");
	}

	public static function NOT_ENOUGH_ARGS(): InternalErrorException{
		return new InternalErrorException(InternalErrorCodes::NOT_ENOUGH_ARGS, "Php-doc метода содержит упоминание не всех аргументов функции");
	}

	public static function UNKNOWN_PARAMETER_TYPE(): InternalErrorException{
		return new InternalErrorException(InternalErrorCodes::UNKNOWN_PARAMETER_TYPE, "Тип аргумента функции не является примитивным");
	}

	public static function PRISMAFRAME_IS_SINGLETON(): InternalErrorException{
		return new InternalErrorException(InternalErrorCodes::PRISMAFRAME_IS_SINGLETON, "PrismaFrame можно запускать только в 1 экземпляре");
	}

}