<?php

declare(strict_types = 1);

namespace GreenWix\prismaFrame\type\validators;

use GreenWix\prismaFrame\type\TypeValidator;
use GreenWix\prismaFrame\type\validators\exception\BadValidationException;

class FloatValidator extends TypeValidator {

  public function getFullTypeName(): string {
    return "float";
  }

  public function createAlsoArrayType(): bool {
    return true;
  }

  /**
   * @throws BadValidationException
   */
  public function validateAndGetValue(string $input, array $extraData): float {
    if (is_numeric($input)) {
      return (float)$input;
    }

    throw new BadValidationException();
  }
}