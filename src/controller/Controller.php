<?php

namespace GreenWix\prismaFrame\controller;

use GreenWix\prismaFrame\controller\exception\UnknownMethodException;
use GreenWix\prismaFrame\type\TypeManagerException;
use GreenWix\prismaFrame\type\validators\exception\BadValidationException;

abstract class Controller {

  /** @var Method[] */
  public array $methods = [];

  /**
   * @param mixed[] $args
   *
   * @throws exception\WrongHttpMethodException
   * @throws exception\BadInputException
   * @throws UnknownMethodException
   * @throws TypeManagerException
   * @throws BadValidationException
   */
  final public function callMethod(string $methodName, string $httpMethod, array $args): array {
    $this->checkIfMethodExists($methodName);

    return $this->methods[$methodName]->invoke($httpMethod, $args);
  }

  /**
   * @throws UnknownMethodException
   */
  private function checkIfMethodExists(string $methodName): void {
    if (isset($this->methods[$methodName])) {
      return;
    }

    throw new UnknownMethodException("Unknown method");
  }

  abstract public function getName(): string;

}