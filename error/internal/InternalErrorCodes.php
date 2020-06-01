<?php


namespace GreenWix\prismaFrame\error\internal;


interface InternalErrorCodes
{

	const NO_DOC = 0x00; // Метод не содержит php-doс
	const WRONG_RETURN_TYPE = 0x01; // Метод должен возвращать массив
	const NO_SUPPORT_HTTP_METHODS = 0x02; // Php-doc метода не содержит информацию о том, какие http-методы будет он принимать
	const NOT_ENOUGH_ARGS = 0x03; // Указаны не все аргументы функции в php-doc
	const ELEMENT_ALREADY_REGISTERED = 0x05; // Попытка зарегистрировать поддерживаемый тип, который уже зарегистрирован
	const BAD_DOC = 0x06; // Ошибка в php-doc
	const PRISMAFRAME_IS_NOT_STARTED = 0x07; // PrismaFrame еще не запущен и выполнить данное действие нельзя
	const PRISMAFRAME_ALREADY_STARTED = 0x08; // PrismaFrame уже запущен и выполнить данное действие нельзя
	const WRONG_HTTP_METHOD = 0x09; // Метод хочет принимать HTTP методы, которые не поддерживаются PrismaFrame
	const UNKNOWN_PARAMETER_TYPE = 0x0a;
	const WRONG_ARGS_ORDER = 0x0b; // Порядок аргументов в php-doc не совпадает с порядком аргументов функции

}