<?php


namespace GreenWix\prismaFrame\error;


use Exception;

class PrismaException extends Exception
{

	public $httpCode = 403;
	public $id;

	public function __construct(int $id, string $message = "", int $httpCode = HTTPCodes::INTERNAL_SERVER_ERROR, Exception $previous = null)
	{
		if($httpCode !== null) {
			$this->httpCode = $httpCode;
		}

		$this->id = $id;
		parent::__construct($message, 0, $previous);
	}
}