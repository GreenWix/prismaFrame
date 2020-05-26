<?php


namespace SociallHouse\prismaFrame\error\internal;


interface InternalErrorCodes
{

	const NO_DOC = 0x00; // Метод не содержит php-doс
	const WRONG_RETURN_TYPE = 0x01; // Метод должен возвращать массив
	const NO_SUPPORT_HTTP_METHODS = 0x02; // Php-doc метода не содержит информацию о том, какие http-методы будет он принимать
	const NOT_ENOUGH_ARGS = 0x03; // Указаны не все аргументы функции в php-doc
	const PRISMAFRAME_IS_SINGLETON = 0x04; // PrismaFrame можно запускать только в одном экземпляре
	const UNKNOWN_PARAMETER_TYPE = 0x0a;

}