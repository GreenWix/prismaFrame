<?php


namespace GreenWix\prismaFrame\error;

use GreenWix\prismaFrame\error\runtime\RuntimeErrorCodes;
use GreenWix\prismaFrame\error\security\SecurityErrorCodes;
use GreenWix\prismaFrame\PrismaFrame;
use GreenWix\prismaFrame\Response;
use Throwable;

final class Error
{

	private function __construct(){}

	public static function make(PrismaFrame $prismaFrame, Throwable $e): Response{
		if($e instanceof PrismaException) {
			$id = $e->id;
			$httpCode = $e->httpCode;
		}else{
			$id = SecurityErrorCodes::INTERNAL_EXCEPTION;
			$httpCode = HTTPCodes::INTERNAL_SERVER_ERROR;
		}
		$message = $e->getMessage();

		if (!$prismaFrame->isDebug() && $httpCode === HTTPCodes::INTERNAL_SERVER_ERROR) {
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