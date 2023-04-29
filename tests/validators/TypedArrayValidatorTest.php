<?php

namespace GreenWix\prismaFrame\tests\validators;

use GreenWix\prismaFrame\type\TypeManager;
use GreenWix\prismaFrame\type\validators\BoolValidator;
use GreenWix\prismaFrame\type\validators\TypedArrayTypeValidator;

class TypedArrayValidatorTest extends ValidatorTestCase {

  protected function getValidator(): TypedArrayTypeValidator {
    $typeManager = new TypeManager();

    // Проверки на примере bool валидатора
    return new TypedArrayTypeValidator('bool', $typeManager);
  }

  /**
   * @return bool[][]
   */
  protected function getTestValues(): array {
    return [
      'true,false' => [true, false],
      '1,0,true' => [true, false, true]
    ];
  }

  /**
   * @return string[]
   */
  protected function getBadValues(): array {
    return [
      'test',
      '2,1,0',
      'true,'
    ];
  }
}