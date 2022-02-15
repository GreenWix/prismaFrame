<?php

declare(strict_types=1);


namespace GreenWix\prismaFrame\type\base;


use GreenWix\prismaFrame\error\runtime\RuntimeError;
use GreenWix\prismaFrame\error\runtime\RuntimeErrorException;
use GreenWix\prismaFrame\type\TypeValidator;
use Throwable;

class JsonTypeValidator extends TypeValidator {

	public function createAlsoArrayType(): bool {
		return true;
	}

	/**
	 * @throws RuntimeErrorException
	 */
	public function validateAndGetValue(string $var, array $extraData): array {
		try {
			return json_decode($var, true, JSON_THROW_ON_ERROR);
		}catch(Throwable $e){
			throw RuntimeError::BAD_VALIDATION_RESULT("Wrong json: " . $e->getMessage());
		}
	}
}