<?php

declare(strict_types=1);


namespace GreenWix\prismaFrame\type\validators;


use GreenWix\prismaFrame\error\runtime\RuntimeError;
use GreenWix\prismaFrame\error\runtime\RuntimeErrorException;
use GreenWix\prismaFrame\type\TypeValidator;

class FloatTypeValidator extends TypeValidator {

	public function getFullTypeName(): string {
		return "float";
	}

	public function createAlsoArrayType(): bool {
		return true;
	}

	/**
	 * @throws RuntimeErrorException
	 */
	public function validateAndGetValue(string $var, array $extraData): float {
		if(is_numeric($var)) {
			return (float)$var;
		}

		throw RuntimeError::BAD_VALIDATION_RESULT();
	}
}