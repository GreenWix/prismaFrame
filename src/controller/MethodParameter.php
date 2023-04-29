<?php

namespace GreenWix\prismaFrame\controller;

use GreenWix\prismaFrame\type\TypeManager;
use GreenWix\prismaFrame\type\TypeManagerException;
use GreenWix\prismaFrame\type\validators\exception\BadValidationException;

class MethodParameter {

  public bool $required = false;
  public string $name;
  public string $typeName;

  /** @var string[] */
  public array $extraData;

  private TypeManager $typeManager;

  public function __construct(TypeManager $typeManager, string $name, string $typeName, array $extraData, bool $required) {
    $this->name = $name;
    $this->typeName = $typeName;
    $this->extraData = $extraData;
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

    return $typeManager->validateTypedInput($this->typeName, $input, $this->extraData);
  }

}