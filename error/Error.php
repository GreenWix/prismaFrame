<?php


namespace SociallHouse\prismaFrame\error;

use SociallHouse\prismaFrame\error\runtime\RuntimeErrorCodes;
use SociallHouse\prismaFrame\PrismaFrame;
use SociallHouse\prismaFrame\Response;

final class Error
{

	private function __construct(){}

	public static function make(int $id, string $message, int $httpCode = HTTPCodes::BAD_REQUEST): Response{
		if(!PrismaFrame::isDebug() && $httpCode === HTTPCodes::INTERNAL_SERVER_ERROR){
			$message = "Internal server error";
			$id = RuntimeErrorCodes::SECURITY;
		}

		return new Response([
			"error" => [
				"code" => $id,
				"message" => $message,
			]
		], $httpCode);
	}

}