<?php

namespace GreenWix\prismaFrame\controller;

use GreenWix\prismaFrame\controller\exception\UnknownMethodException;
use GreenWix\prismaFrame\error\PrismaException;
use GreenWix\prismaFrame\type\TypeManagerException;
use GreenWix\prismaFrame\type\validators\exception\BadValidationException;

abstract class ControllerBase {

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
   * @throws PrismaException
   */
  final public function callMethod(string $methodName, string $httpMethod, array $args): array {
    $this->checkIfMethodExists($methodName);

    $this->beforeCall();

    return $this->methods[$methodName]->invoke($httpMethod, $args);
  }

  /**
   * Проверки перед вызовом
   * @throws PrismaException
   */
  public function beforeCall(): void {

  }

  /**
   * @throws UnknownMethodException
   */
  private function checkIfMethodExists(string $methodName): void {
    if (isset($this->methods[$methodName])) {
      return;
    }

    throw new UnknownMethodException();
  }

  abstract public function getName(): string;

}