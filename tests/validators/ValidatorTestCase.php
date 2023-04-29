<?php

namespace GreenWix\prismaFrame\tests\validators;

use GreenWix\prismaFrame\type\TypeValidator;
use GreenWix\prismaFrame\type\validators\exception\BadValidationException;
use PHPUnit\Framework\TestCase;
use Throwable;

abstract class ValidatorTestCase extends TestCase {

  abstract protected function getValidator(): TypeValidator;

  /**
   * Тестовые корректные значения
   * array<string, any>
   * @return any[]
   */
  abstract protected function getTestValues(): array;

  /**
   * Тестовые некорректные значения
   * @return string[]
   */
  abstract protected function getBadValues(): array;

  public function testCorrectValues(): void {
    $v = $this->getValidator();

    foreach ($this->getTestValues() as $strValue => $value) {
      $val = $v->validateAndGetValue($strValue, []);

      $this->assertEquals($value, $val);
    }
  }

  public function testNoValue(): void {
    $this->expectException(BadValidationException::class);

    $v = $this->getValidator();

    $v->validateAndGetValue("", []);
  }

  public function testBadValues(): void {
    $v = $this->getValidator();

    $badValues = $this->getBadValues();
    if (empty($badValues)) {
      $thisClass = get_class($this);
      $this->addWarning("Bad values are empty in {$thisClass}");
      $this->expectNotToPerformAssertions();
      return;
    }

    foreach ($badValues as $strValue) {
      try {
        $v->validateAndGetValue($strValue, []);
        $this->fail("No error for value {$strValue}");
      } catch (BadValidationException $exception) {
        // все ок
        $this->assertEquals("Wrong input", $exception->getMessage());
      } catch (Throwable $exception) {
        $this->fail("Unexpected exception: " . $exception->__toString());
      }
    }
  }

}