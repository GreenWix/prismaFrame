<?php


namespace SociallHouse\prismaFrame\controller;


use SociallHouse\prismaFrame\error\runtime\RuntimeErrorException;

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
	 * @param $reason
	 * @return bool
	 * @throws RuntimeErrorException
	 */
	public function validate(string $input, &$var, &$reason): bool{
		$r = "";
		foreach ($this->types as $type){
			if(Checker::validateSupportedType($type, $input, $var, $this->extraData, $r)){
				return true;
			}elseif($r !== ""){
				$reason = $r;
			}
		}
		return false;
	}

}