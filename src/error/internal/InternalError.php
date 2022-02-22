<?php


namespace GreenWix\prismaFrame\error\internal;


use ReflectionNamedType;

final class InternalError
{

	/*
	 * Internal ошибки проявляются только при запуске процесса, когда происходит валидация контроллеров и их методов
	 * Во время работы процесса данные ошибки не появляются
	 */

	private function __construct(){}

	public static function NO_DOC(string $controller, string $method): InternalErrorException{
		return new InternalErrorException(InternalErrorCodes::NO_DOC, "Метод \"{$controller}.{$method}\" не содержит php-doc");
	}

	public static function WRONG_RETURN_TYPE(string $typeName, ?ReflectionNamedType $functionReturnType, array $doc, string $controller, string $method): InternalErrorException{
		$functionReturnTypeName = $functionReturnType === null ? "<пусто>" : $functionReturnType->getName();

		return new InternalErrorException(InternalErrorCodes::WRONG_RETURN_TYPE, "Метод \"{$controller}.{$method}\" должен возвращать тип ".$typeName . ". " .
		"Возвращаемый тип функции: " . $functionReturnTypeName . ". PhpDoc: " . implode("\n", $doc));
	}

	public static function NO_SUPPORT_HTTP_METHODS(): InternalErrorException{
		return new InternalErrorException(InternalErrorCodes::NO_SUPPORT_HTTP_METHODS, "Php-doc метода должен содержать @httpMethod <GET|POST|PATCH|PUT или несколько методов перечисленных через \"|\">");
	}

	public static function WRONG_HTTP_METHOD(string $controller, string $method, string $httpMethod): InternalErrorException{
		return new InternalErrorException(InternalErrorCodes::WRONG_HTTP_METHOD, "Ошибка в Php-doc'е метода \"{$controller}.{$method}\". HTTP метод \"{$httpMethod}\" не поддерживается");
	}

	public static function NOT_ENOUGH_ARGS(string $controller, string $method): InternalErrorException{
		return new InternalErrorException(InternalErrorCodes::NOT_ENOUGH_ARGS, "Php-doc метода \"{$controller}.{$method}\" содержит упоминание не всех аргументов функции");
	}

	public static function UNKNOWN_PARAMETER_TYPE(string $controller, string $method, string $typeName): InternalErrorException{
		return new InternalErrorException(InternalErrorCodes::UNKNOWN_PARAMETER_TYPE, "Тип {$typeName} аргумента метода \"{$controller}.{$method}\" не является поддерживаемым");
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

	public static function NO_SECURITY(): InternalErrorException{
		return new InternalErrorException(InternalErrorCodes::NO_SECURITY, "Не установлен Security менеджер. Установите его при помощи метода PrismaFrame::setSecurity()");
	}

	public static function WRONG_ARGS_ORDER(string $controller, string $method): InternalErrorException{
		return new InternalErrorException(InternalErrorCodes::WRONG_ARGS_ORDER, "Порядок аргументов в php-doc метода \"{$controller}.{$method}\" не совпадает с порядком аргументов функции");
	}

}