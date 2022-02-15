<?php

declare(strict_types=1);


namespace GreenWix\prismaFrame\type\base;


use GreenWix\prismaFrame\error\runtime\RuntimeError;
use GreenWix\prismaFrame\error\runtime\RuntimeErrorException;
use GreenWix\prismaFrame\type\TypeValidator;

class BoolTypeValidator extends TypeValidator {

	public function createAlsoArrayType(): bool {
		return true;
	}

	/**
	 * @throws RuntimeErrorException
	 */
	public function validateAndGetValue(string $var, array $extraData): bool{
		switch($var){
			case 'true':
			case '1':
				return true;
			case 'false':
			case '0':
				return false;
		}

		throw RuntimeError::BAD_VALIDATION_RESULT();
	}
}