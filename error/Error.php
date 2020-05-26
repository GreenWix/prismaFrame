<?php


namespace SociallHouse\prismaFrame\error;

use SociallHouse\prismaFrame\error\runtime\RuntimeErrorCodes;

final class Error
{

	/** @var array */
	public $response;

	/** @var int */
	public $httpCode;

	private function __construct(array $response, int $httpCode){
		$this->response = $response;
		$this->httpCode = $httpCode;
	}

	public static function make(int $id, string $message, int $httpCode = HTTPCodes::BAD_REQUEST): Error{
		//todo получать это значение откуда-то
		$debug = true;

		if(!$debug && $httpCode === HTTPCodes::INTERNAL_SERVER_ERROR){
			$message = "Internal server error";
			$id = RuntimeErrorCodes::SECURITY;
		}

		return new Error([
			"error" => [
				"code" => $id,
				"message" => $message,
			]
		], $httpCode);
	}

}