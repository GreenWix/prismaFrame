<?php


namespace GreenWix\prismaFrame\error;

use GreenWix\prismaFrame\Response;
use Throwable;

final class Error {

	public static function make(bool $isDebug, Throwable $e): Response {
		[$id, $httpCode] = self::getIdAndHttpCode($e);
		$message = $e->getMessage();

		if (!$isDebug && $httpCode === HTTPCodes::INTERNAL_SERVER_ERROR) {
			$message = "Internal server error";
			$id = ErrorCodes::INTERNAL_ERROR;
		}

		return new Response([
			"error" => [
				"code" => $id,
				"message" => $message,
			]
		], $httpCode);
	}

	protected static function getIdAndHttpCode(Throwable $e): array {
		if ($e instanceof PrismaException) {
			$id = $e->id;
			$httpCode = $e->httpCode;
		} else {
			$id = ErrorCodes::INTERNAL_ERROR;
			$httpCode = HTTPCodes::INTERNAL_SERVER_ERROR;
		}

		return [$id, $httpCode];
	}

	private function __construct() {
	}

}