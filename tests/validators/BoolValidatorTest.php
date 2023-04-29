<?php

namespace GreenWix\prismaFrame\tests\validators;

use GreenWix\prismaFrame\type\validators\BoolValidator;

class BoolValidatorTest extends ValidatorTestCase {

  protected function getValidator(): BoolValidator {
    return new BoolValidator();
  }

  /**
   * @return bool[]
   */
  protected function getTestValues(): array {
    return [
      '1' => true,
      'true' => true,
      '0' => false,
      'false' => false
    ];
  }

  /**
   * @return string[]
   */
  protected function getBadValues(): array {
    return [
      '2', 'True', 'False', '-1'
    ];
  }
}