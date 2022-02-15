<?php


namespace GreenWix\prismaFrame\controller;


use GreenWix\prismaFrame\error\runtime\RuntimeError;
use GreenWix\prismaFrame\error\runtime\RuntimeErrorException;
use GreenWix\prismaFrame\PrismaFrame;
use GreenWix\prismaFrame\type\TypeManager;
use Throwable;

class MethodParameter
{

	/** @var bool */
	public $required = false;

	/** @var string */
	public $name;

	/** @var string */
	public $typeName;

	/** @var string[] */
	public $extraData;

	/** @var TypeManager */
	private $typeManager;

	public function __construct(TypeManager $typeManager, string $name, string $typeName, array $extraData, bool $required){
		$this->name = $name;
		$this->typeName = $typeName;
		$this->extraData = $extraData;
		$this->required = $required;

		$this->typeManager = $typeManager;
	}

	/**
	 * @param string $input
	 * @return mixed
	 * @throws RuntimeErrorException
	 */
	public function validateAndGetValue(string $input){
		$typeManager = $this->typeManager;

		return $typeManager->validateTypedInput($this->typeName, $input, $this->extraData);
	}

}