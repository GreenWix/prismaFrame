<?php

declare(strict_types=1);


namespace GreenWix\prismaFrame\type\validators;


use GreenWix\prismaFrame\error\runtime\RuntimeError;
use GreenWix\prismaFrame\error\runtime\RuntimeErrorException;
use GreenWix\prismaFrame\type\TypeValidator;

class FloatValidator extends TypeValidator {

	public function getFullTypeName(): string {
		return "float";
	}

	public function createAlsoArrayType(): bool {
		return true;
	}

	/**
	 * @throws RuntimeErrorException
	 */
	public function validateAndGetValue(string $input, array $extraData): float {
		if(is_numeric($input)) {
			return (float)$input;
		}

		throw RuntimeError::BAD_VALIDATION_RESULT();
	}
}