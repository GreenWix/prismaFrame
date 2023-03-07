<?php

namespace GreenWix\prismaFrame\error;

use Exception;

class InternalErrorException extends PrismaException {

  public function __construct(string $message = "", int $httpCode = HTTPCodes::INTERNAL_SERVER_ERROR, Exception $previous = null) {
    parent::__construct(ErrorCodes::INTERNAL_ERROR, $message, $httpCode, $previous);
  }

}