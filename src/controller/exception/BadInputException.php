<?php

declare(strict_types=1);


namespace GreenWix\prismaFrame\controller\exception;


use Exception;
use GreenWix\prismaFrame\error\ErrorCodes;
use GreenWix\prismaFrame\error\HTTPCodes;

class BadInputException extends ControllerException {

	public function __construct(string $message, Exception $previous = null) {
		parent::__construct(ErrorCodes::BAD_INPUT, $message, HTTPCodes::BAD_REQUEST, $previous);
	}

}