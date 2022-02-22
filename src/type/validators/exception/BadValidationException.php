<?php

declare(strict_types=1);


namespace GreenWix\prismaFrame\type\validators\exception;


use Exception;
use GreenWix\prismaFrame\error\HTTPCodes;
use GreenWix\prismaFrame\error\runtime\RuntimeErrorCodes;

class BadValidationException extends ValidatorException {

	public function __construct(string $message = "Wrong input", int $httpCode = HTTPCodes::INTERNAL_SERVER_ERROR, Exception $previous = null) {
		parent::__construct(RuntimeErrorCodes::BAD_VALIDATION_RESULT, $message, $httpCode, $previous);
	}

}