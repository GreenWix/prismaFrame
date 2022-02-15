<?php

declare(strict_types=1);


namespace GreenWix\prismaFrame\type\base;


use GreenWix\prismaFrame\type\TypeValidator;

class StringTypeValidator extends TypeValidator {

	public function createAlsoArrayType(): bool {
		return true;
	}

	public function validateAndGetValue(string $var, array $extraData): string{
		return $var;
	}
}