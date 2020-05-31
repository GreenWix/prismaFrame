<?php


namespace SociallHouse\prismaFrame\error;


class PrismaException extends \Exception
{

	public $httpCode = 403;
	public $id;

	public function __construct(int $id, string $message = "", int $httpCode = HTTPCodes::INTERNAL_SERVER_ERROR)
	{
		if($httpCode !== null) {
			$this->httpCode = $httpCode;
		}

		$this->id = $id;
		parent::__construct($message, 0, null);
	}
}