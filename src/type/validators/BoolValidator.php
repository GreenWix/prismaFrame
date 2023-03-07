<?php

declare(strict_types = 1);

namespace GreenWix\prismaFrame\type\validators;

use GreenWix\prismaFrame\type\TypeValidator;
use GreenWix\prismaFrame\type\validators\exception\BadValidationException;

class BoolValidator extends TypeValidator {

  public function getFullTypeName(): string {
    return "bool";
  }

  public function createAlsoArrayType(): bool {
    return true;
  }

  /**
   * @throws BadValidationException
   */
  public function validateAndGetValue(string $input, array $extraData): bool {
    switch ($input) {
      case 'true':
      case '1':
        return true;
      case 'false':
      case '0':
        return false;
    }

    throw new BadValidationException();
  }
}