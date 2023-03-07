<?php

declare(strict_types = 1);

namespace GreenWix\prismaFrame\controller\exception;

use Exception;
use GreenWix\prismaFrame\error\ErrorCodes;
use GreenWix\prismaFrame\error\HTTPCodes;

class UnknownMethodException extends ControllerException {

  public function __construct(string $message, Exception $previous = null) {
    parent::__construct(ErrorCodes::UNKNOWN_METHOD, $message, HTTPCodes::NOT_FOUND, $previous);
  }

}