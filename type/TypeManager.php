<?php

declare(strict_types=1);


namespace GreenWix\prismaFrame\type;


use GreenWix\prismaFrame\error\runtime\RuntimeError;
use GreenWix\prismaFrame\error\runtime\RuntimeErrorException;
use GreenWix\prismaFrame\PrismaFrame;
use GreenWix\prismaFrame\type\base\ArrayTypeValidator;
use GreenWix\prismaFrame\type\base\BoolTypeValidator;
use GreenWix\prismaFrame\type\base\FloatTypeValidator;
use GreenWix\prismaFrame\type\base\IntTypeValidator;
use GreenWix\prismaFrame\type\base\JsonTypeValidator;
use GreenWix\prismaFrame\type\base\StringTypeValidator;

class TypeManager {

	/** @var array */
	private $types = [];

	public function __construct(){
		$this->initBaseSupportedTypes();
	}

	protected function initBaseSupportedTypes(): void{
		$this->addTypeValidator(new ArrayTypeValidator());
		$this->addTypeValidator(new BoolTypeValidator());
		$this->addTypeValidator(new FloatTypeValidator());
		$this->addTypeValidator(new IntTypeValidator());
		$this->addTypeValidator(new JsonTypeValidator());
		$this->addTypeValidator(new StringTypeValidator());
	}

	public function addTypeValidator(TypeValidator $validator): void{
		$this->types[$validator->getName()] = $validator;
		if($validator->createAlsoArrayType()){
			$this->addTypeValidator(new ArrayDerivedTypeValidator("", [], $validator->getName()));
		}
	}

	/**
	 * @param string $name
	 * @param string $input
	 * @param array $extraData
	 * @return mixed
	 * @throws RuntimeErrorException
	 */
	public function validateSupportedType(string $name, string $input, array $extraData = []){
		if(!isset($this->types[$name])){
			throw RuntimeError::UNKNOWN_PARAMETER_TYPE($name);
		}

		$type = $this->types[$name];

		return $type->validateAndGetValue($input, $extraData);
	}

}