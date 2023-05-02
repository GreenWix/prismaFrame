<?php

namespace GreenWix\prismaFrame\type\validators;

use GreenWix\prismaFrame\type\TypeManager;
use GreenWix\prismaFrame\type\TypeManagerException;
use GreenWix\prismaFrame\type\TypeValidator;
use GreenWix\prismaFrame\type\validators\exception\BadValidationException;
use ReflectionAttribute;

class TypedArrayTypeValidator extends TypeValidator {

  private string $typeName;
  private TypeManager $typeManager;

  public function __construct(string $typeName, TypeManager $manager) {
    $this->typeName = $typeName;
    $this->typeManager = $manager;
  }

  public function getFullTypeName(): string {
    return $this->typeName . '[]';
  }

  public function createAlsoArrayType(): bool {
    return false;
  }

  /**
   * @param ReflectionAttribute[] $attributes
   * @return any[]
   *
   * @throws TypeManagerException
   * @throws BadValidationException
   */
  public function validateAndGetValue(string $input, array $attributes): array {
    $result = [];
    $elements = explode(",", $input);

    foreach ($elements as $element) {
      $value = $this->typeManager->validateTypedInput($this->typeName, $element, $attributes);

      $result[] = $value;
    }
    return $result;
  }

}