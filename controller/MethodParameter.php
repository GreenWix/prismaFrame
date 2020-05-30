<?php


namespace SociallHouse\prismaFrame\controller;


use SociallHouse\prismaFrame\error\internal\InternalErrorException;

class MethodParameter
{

	/** @var bool */
	public $required = false;

	/** @var string */
	public $name;

	/** @var string[] */
	public $types;

	/** @var string[] */
	public $extraData;

	/**
	 * ControllerParameter constructor.
	 * @param string $name
	 * @param string[] $types
	 * @param array $extraData
	 * @param bool $required
	 */
	public function __construct(string $name, array $types, array $extraData, bool $required)
	{
		$this->name = $name;
		$this->types = $types;
		$this->extraData = $extraData;
		$this->required = $required;
	}

	/**
	 * @param string $input
	 * @param $var
	 * @param array $extraData
	 * @return bool
	 * @throws InternalErrorException
	 */
	public function validate(string $input, &$var): bool{
		foreach ($this->types as $type){
			if(Checker::validateSupportedType($type, $input, $var, $this-$this->extraData)) return true;
		}
		return false;
	}

}