<?php

declare(strict_types=1);


namespace GreenWix\prismaFrame\handler\exception;


use Exception;
use GreenWix\prismaFrame\error\ErrorCodes;
use GreenWix\prismaFrame\error\HTTPCodes;

class VersionException extends RequestException {

	public function __construct(string $message, int $httpCode = HTTPCodes::INTERNAL_SERVER_ERROR, Exception $previous = null) {
		parent::__construct(ErrorCodes::WRONG_VERSION, $message, $httpCode, $previous);
	}

}