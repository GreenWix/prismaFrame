<?php

namespace GreenWix\prismaFrame\controller;

use GreenWix\prismaFrame\type\TypeManager;
use GreenWix\prismaFrame\type\TypeManagerException;
use GreenWix\prismaFrame\type\validators\exception\BadValidationException;
use ReflectionAttribute;

class MethodParameter {

  public bool $required = false;
  public string $name;
  public string $typeName;

  /** @var ReflectionAttribute[] */
  public array $attributes;

  private TypeManager $typeManager;

  /**
   * @param ReflectionAttribute[] $attributes
   */
  public function __construct(TypeManager $typeManager, string $name, string $typeName, array $attributes, bool $required) {
    $this->name = $name;
    $this->typeName = $typeName;
    $this->attributes = $attributes;
    $this->required = $required;

    $this->typeManager = $typeManager;
  }

  /**
   * @return any
   *
   * @throws TypeManagerException
   * @throws BadValidationException
   */
  public function validateAndGetValue(string $input) {
    $typeManager = $this->typeManager;

    return $typeManager->validateTypedInput($this->typeName, $input, $this->attributes);
  }

}