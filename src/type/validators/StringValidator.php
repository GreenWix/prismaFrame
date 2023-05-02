<?php

declare(strict_types = 1);

namespace GreenWix\prismaFrame\type\validators;

use GreenWix\prismaFrame\type\TypeValidator;

class StringValidator extends TypeValidator {

  public function getFullTypeName(): string {
    return "string";
  }

  public function createAlsoArrayType(): bool {
    return true;
  }

  public function validateAndGetValue(string $input, array $attributes): string {
    return $input;
  }

}