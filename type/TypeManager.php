<?php

declare(strict_types=1);


namespace GreenWix\prismaFrame\type;


use GreenWix\prismaFrame\error\runtime\RuntimeError;
use GreenWix\prismaFrame\error\runtime\RuntimeErrorException;
use GreenWix\prismaFrame\PrismaFrame;
use GreenWix\prismaFrame\type\validators\ArrayTypeValidator;
use GreenWix\prismaFrame\type\validators\BoolTypeValidator;
use GreenWix\prismaFrame\type\validators\FloatTypeValidator;
use GreenWix\prismaFrame\type\validators\IntTypeValidator;
use GreenWix\prismaFrame\type\validators\StringTypeValidator;

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
		$this->addTypeValidator(new StringTypeValidator());
	}

	public function addTypeValidator(TypeValidator $validator): void{
		$this->types[$validator->getFullTypeName()] = $validator;
		if($validator->createAlsoArrayType()){
			$this->addTypeValidator(new TypedArrayTypeValidator($validator->getFullTypeName(), $this));
		}
	}

	/**
	 * @param string $typeName
	 * @param string $input
	 * @param array $extraData
	 * @return mixed
	 * @throws RuntimeErrorException
	 */
	public function validateTypedInput(string $typeName, string $input, array $extraData = []){
		if(!isset($this->types[$typeName])){
			throw RuntimeError::UNKNOWN_PARAMETER_TYPE($typeName);
		}

		$type = $this->types[$typeName];

		return $type->validateAndGetValue($input, $extraData);
	}

	public function hasTypeValidator(string $typeName): bool{
		return isset($this->types[$typeName]);
	}

}