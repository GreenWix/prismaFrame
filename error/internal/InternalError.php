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

	public static function WRONG_RETURN_TYPE(string $typeName = "array"): InternalErrorException{
		return new InternalErrorException(InternalErrorCodes::WRONG_RETURN_TYPE, "Метод должен возвращать тип ".$typeName);
	}

	public static function NO_SUPPORT_HTTP_METHODS(): InternalErrorException{
		return new InternalErrorException(InternalErrorCodes::NO_SUPPORT_HTTP_METHODS, "Php-doc метода должен содержать @method <GET|POST|PATCH|PUT>");
	}

	public static function NOT_ENOUGH_ARGS(): InternalErrorException{
		return new InternalErrorException(InternalErrorCodes::NOT_ENOUGH_ARGS, "Php-doc метода содержит упоминание не всех аргументов функции");
	}

	public static function UNKNOWN_PARAMETER_TYPE(string $typeName): InternalErrorException{
		return new InternalErrorException(InternalErrorCodes::UNKNOWN_PARAMETER_TYPE, "Тип {$typeName} аргумента функции не является поддерживаемым");
	}

	public static function ELEMENT_ALREADY_REGISTERED(string $typeName, string $elementName): InternalErrorException{
		return new InternalErrorException(InternalErrorCodes::ELEMENT_ALREADY_REGISTERED, $typeName . " " . $elementName . " уже зарегистрирован");
	}

	public static function BAD_DOC(string $message): InternalErrorException{
		return new InternalErrorException(InternalErrorCodes::BAD_DOC, $message);
	}

	public static function PRISMAFRAME_IS_NOT_STARTED(string $message): InternalErrorException{
		return new InternalErrorException(InternalErrorCodes::PRISMAFRAME_IS_NOT_STARTED, $message);
	}

	public static function PRISMAFRAME_ALREADY_STARTED(string $message): InternalErrorException{
		return new InternalErrorException(InternalErrorCodes::PRISMAFRAME_ALREADY_STARTED, $message);
	}

}