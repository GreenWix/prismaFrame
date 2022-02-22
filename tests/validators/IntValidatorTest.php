<?php

declare(strict_types=1);


namespace GreenWix\prismaFrame\tests\validators;


use GreenWix\prismaFrame\error\runtime\RuntimeErrorException;
use GreenWix\prismaFrame\type\validators\exception\exception\IntValidator;
use PHPUnit\Framework\TestCase;

class IntValidatorTest extends TestCase {

	private function getValidator(): IntValidator{
		return new IntValidator();
	}

	public function testCorrectValue(): void{
		$v = $this->getValidator();

		$a = 5;

		$val = $v->validateAndGetValue("107", []);

		$this->assertEquals(107, $val);
	}

	public function testNoValue(): void{
		$this->expectException(RuntimeErrorException::class);

		$v = $this->getValidator();

		$val = $v->validateAndGetValue("", []);
	}

}