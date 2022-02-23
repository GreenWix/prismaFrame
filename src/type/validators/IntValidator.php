<?php

declare(strict_types=1);


namespace GreenWix\prismaFrame\type\validators;


use GreenWix\prismaFrame\type\TypeValidator;
use GreenWix\prismaFrame\type\validators\exception\BadValidationException;

class IntValidator extends TypeValidator {

	public function getFullTypeName(): string {
		return "int";
	}

	public function createAlsoArrayType(): bool {
		return true;
	}

	/**
	 * @throws BadValidationException
	 */
	public function validateAndGetValue(string $input, array $extraData): int {
		if (is_numeric($input)) {
			return intval($input);
		}

		throw new BadValidationException();
	}
}