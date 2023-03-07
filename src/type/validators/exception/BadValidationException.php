<?php

declare(strict_types = 1);

namespace GreenWix\prismaFrame\type\validators\exception;

use Exception;
use GreenWix\prismaFrame\error\ErrorCodes;
use GreenWix\prismaFrame\error\HTTPCodes;

class BadValidationException extends ValidatorException {

  public function __construct(string $message = "Wrong input", int $httpCode = HTTPCodes::INTERNAL_SERVER_ERROR, Exception $previous = null) {
    parent::__construct(ErrorCodes::BAD_VALIDATION_RESULT, $message, $httpCode, $previous);
  }

}