<?php

declare(strict_types=1);


namespace GreenWix\prismaFrame\tests\validators;


use GreenWix\prismaFrame\type\validators\IntValidator;

class IntValidatorTest extends ValidatorTestCase {

	protected function getValidator(): IntValidator {
		return new IntValidator();
	}

  /**
   * @return int[]
   */
  protected function getTestValues(): array {
    return [
      "107" => 107
    ];
  }

  protected function getBadValues(): array {
    return ["test"];
  }
}