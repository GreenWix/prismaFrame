<?php


namespace GreenWix\prismaFrame\type;


abstract class SupportedType
{

	public $input;
	public $extraData;

	public function __construct(string $input = "", array $extraData = []){
		$this->input = $input;
		$this->extraData = $extraData;
	}

	public function getName(): string{
		return array_pop(explode("\\", get_class($this)));
	}

	abstract public function isArrayType(): bool;

	abstract public function validate(string $var, array $extraData);

}