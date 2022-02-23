<?php


namespace GreenWix\prismaFrame\error\internal;


use Exception;
use GreenWix\prismaFrame\error\HTTPCodes;
use GreenWix\prismaFrame\error\PrismaException;

class InternalErrorException extends PrismaException
{

	public function __construct(string $message = "", int $httpCode = HTTPCodes::INTERNAL_SERVER_ERROR, Exception $previous = null) {
		parent::__construct(InternalErrorCodes::INTERNAL_ERROR, $message, $httpCode, $previous);
	}

}