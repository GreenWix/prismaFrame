<?php

namespace GreenWix\prismaFrame\type;

abstract class TypeValidator {

  /**
   * Возвращает полный неймспейс класса, который формирует данный валидатор
   * @return string ClassName::class, например User::class
   */
  abstract public function getFullTypeName(): string;

  /**
   * Возвращает имя класса без неймспейса.
   * @return string
   */
  public function getDocTypeName(): string {
    $name = $this->getFullTypeName(); // \namespace\a\b\c\SomeClass

    $parts = explode("\\", $name);  // [namespace, a, b, c, SomeClass]

    return array_pop($parts); // SomeClass
  }

  abstract public function createAlsoArrayType(): bool;

  /**
   * @param string[] $extraData
   * @return any
   */
  abstract public function validateAndGetValue(string $input, array $extraData);

}