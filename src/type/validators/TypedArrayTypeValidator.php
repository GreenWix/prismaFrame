<?php

namespace GreenWix\prismaFrame\type\validators;

use GreenWix\prismaFrame\type\TypeManager;
use GreenWix\prismaFrame\type\TypeManagerException;
use GreenWix\prismaFrame\type\TypeValidator;

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
   * @param string[] $extraData
   * @return any[]
   *
   * @throws TypeManagerException
   */
  public function validateAndGetValue(string $input, array $extraData): array {
    $result = [];
    $elements = explode(",", $input);

    foreach ($elements as $element) {
      $value = $this->typeManager->validateTypedInput($this->typeName, $element, $extraData);

      $result[] = $value;
    }
    return $result;
  }

}