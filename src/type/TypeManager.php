<?php

declare(strict_types = 1);

namespace GreenWix\prismaFrame\type;

use GreenWix\prismaFrame\type\validators\ArrayValidator;
use GreenWix\prismaFrame\type\validators\BoolValidator;
use GreenWix\prismaFrame\type\validators\FloatValidator;
use GreenWix\prismaFrame\type\validators\IntValidator;
use GreenWix\prismaFrame\type\validators\StringValidator;
use GreenWix\prismaFrame\type\validators\TypedArrayTypeValidator;

class TypeManager {

  /** @var TypeValidator[] array<string, TypeValidator> */
  private array $types = [];

  /**
   * @throws TypeManagerException
   */
  public function __construct() {
    $this->initBaseSupportedTypes();
  }

  /**
   * @throws TypeManagerException
   */
  protected function initBaseSupportedTypes(): void {
    $this->addTypeValidator(new ArrayValidator());
    $this->addTypeValidator(new BoolValidator());
    $this->addTypeValidator(new FloatValidator());
    $this->addTypeValidator(new IntValidator());
    $this->addTypeValidator(new StringValidator());
  }

  /**
   * @throws TypeManagerException
   */
  public function addTypeValidator(TypeValidator $validator): void {
    $docTypeName = $validator->getDocTypeName();
    if (isset($this->types[$docTypeName])) {
      throw new TypeManagerException("The type with name \"$docTypeName\" is already busy. Please choose a different name for " . get_class($validator));
    }

    $this->types[$docTypeName] = $validator;

    if ($validator->createAlsoArrayType()) {
      $this->addTypeValidator(new TypedArrayTypeValidator($docTypeName, $this));
    }
  }

  /**
   * @param string[] $extraData
   * @return any
   *
   * @throws TypeManagerException
   */
  public function validateTypedInput(string $typeName, string $input, array $extraData = []) {
    $this->checkTypeValidatorExistence($typeName);

    $type = $this->types[$typeName];

    return $type->validateAndGetValue($input, $extraData);
  }

  public function hasTypeValidator(string $typeName): bool {
    return isset($this->types[$typeName]);
  }

  /**
   * @throws TypeManagerException
   */
  public function getTypeValidator(string $typeName): TypeValidator {
    $this->checkTypeValidatorExistence($typeName);

    return $this->types[$typeName];
  }

  /**
   * @throws TypeManagerException
   */
  public function checkTypeValidatorExistence(string $typeName): void {
    if (!$this->hasTypeValidator($typeName)) {
      throw new TypeManagerException("No validator for type " . $typeName);
    }
  }

}