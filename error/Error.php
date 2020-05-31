<?php


namespace SociallHouse\prismaFrame\error;

use SociallHouse\prismaFrame\error\runtime\RuntimeErrorCodes;
use SociallHouse\prismaFrame\error\security\SecurityErrorCodes;
use SociallHouse\prismaFrame\PrismaFrame;
use SociallHouse\prismaFrame\Response;
use Throwable;

final class Error
{

	private function __construct(){}

	public static function make(Throwable $e): Response{
		if($e instanceof PrismaException) {
			$id = $e->id;
			$httpCode = $e->httpCode;
		}else{
			$id = SecurityErrorCodes::INTERNAL_EXCEPTION;
			$httpCode = HTTPCodes::INTERNAL_SERVER_ERROR;
		}
		$message = $e->getMessage();

		if (!PrismaFrame::isDebug() && $httpCode === HTTPCodes::INTERNAL_SERVER_ERROR) {
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