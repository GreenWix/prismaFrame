<?php

namespace GreenWix\prismaFrame\tests\validators;

use GreenWix\prismaFrame\type\validators\StringValidator;

class StringValidatorTest extends ValidatorTestCase {

  protected function getValidator(): StringValidator {
    return new StringValidator();
  }

  /**
   * @return string[]
   */
  protected function getTestValues(): array {
    return [
      "test" => "test",
      "22" => "22",
      "true" => "true",
      "" => "",
      "null" => "null"
    ];
  }

  /**
   * @inheritDoc
   */
  protected function getBadValues(): array {
    return []; // кажется таких нет
  }


  public function testNoValue(): void {
    $v = $this->getValidator();

    $value = $v->validateAndGetValue("", []);
    $this->assertEquals("", $value);
  }
}