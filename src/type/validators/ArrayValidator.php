<?php

declare(strict_types = 1);

namespace GreenWix\prismaFrame\type\validators;

use GreenWix\prismaFrame\type\TypeValidator;

class ArrayValidator extends TypeValidator {

  public function getFullTypeName(): string {
    return "array";
  }

  public function createAlsoArrayType(): bool {
    return false;
  }

  public function validateAndGetValue(string $input, array $extraData): array {
    return explode(',', $input);
  }

}