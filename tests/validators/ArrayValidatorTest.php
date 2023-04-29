<?php

namespace GreenWix\prismaFrame\tests\validators;

use GreenWix\prismaFrame\type\TypeValidator;
use GreenWix\prismaFrame\type\validators\ArrayValidator;
use GreenWix\prismaFrame\type\validators\exception\BadValidationException;

class ArrayValidatorTest extends ValidatorTestCase {

  protected function getValidator(): TypeValidator {
    return new ArrayValidator();
  }

  /**
   * @return string[][]
   */
  protected function getTestValues(): array {
    return [
      'a' => ['a'],
      'a,b' => ['a','b'],
    ];
  }

  public function testNoValue(): void {
    $v = $this->getValidator();

    $this->assertEquals([], $v->validateAndGetValue("", []));
  }

  /**
   * @return string[]
   */
  protected function getBadValues(): array {
    return [];
  }
}