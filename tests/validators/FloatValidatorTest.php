<?php

namespace GreenWix\prismaFrame\tests\validators;

use GreenWix\prismaFrame\type\validators\FloatValidator;

class FloatValidatorTest extends ValidatorTestCase {

  protected function getValidator(): FloatValidator {
    return new FloatValidator();
  }

  /**
   * @return float[]
   */
  protected function getTestValues(): array {
    return [
      '0.12' => 0.12,
      '-0.4' => -0.4,
      '3' => 3,
      '120.0' => 120,
      '1.' => 1,
      '.1' => 0.1
    ];
  }

  /**
   * @return string[]
   */
  protected function getBadValues(): array {
    return [
      'test',
      '1.2.3',
      '1,2,3',
      '.',
    ];
  }
}