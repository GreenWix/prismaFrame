<?php

declare(strict_types=1);


namespace GreenWix\prismaFrame\type\base;


use GreenWix\prismaFrame\type\TypeValidator;

class ArrayTypeValidator extends TypeValidator {

	public function createAlsoArrayType(): bool {
		return false;
	}

	public function validateAndGetValue(string $var, array $extraData): array {
		return explode(',', $var);
	}
}