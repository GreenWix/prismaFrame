<?php


namespace GreenWix\prismaFrame\type;


abstract class TypeValidator
{

	public function getName(): string{
		$array = explode("\\", get_class($this));
		return array_pop($array);
	}

	abstract public function createAlsoArrayType(): bool;

	abstract public function validateAndGetValue(string $var, array $extraData);

}