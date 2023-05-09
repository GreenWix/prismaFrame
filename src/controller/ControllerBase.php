<?php

namespace GreenWix\prismaFrame\controller;

use GreenWix\prismaFrame\controller\exception\UnknownMethodException;
use GreenWix\prismaFrame\error\PrismaException;
use GreenWix\prismaFrame\settings\RequestOptions;
use GreenWix\prismaFrame\type\TypeManagerException;
use GreenWix\prismaFrame\type\validators\exception\BadValidationException;
use Psr\Http\Message\ServerRequestInterface;

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
  #[NotControllerMethod]
  final public function callMethod(string $methodName, string $httpMethod, array $args, ServerRequestInterface $request, RequestOptions $options): array {
    $this->checkIfMethodExists($methodName);

    $this->beforeCall($methodName, $httpMethod, $args, $request, $options);

    return $this->methods[$methodName]->invoke($httpMethod, $args);
  }

  /**
   * Список обязательных параметров в каждом методе контроллера
   * @return string[] array<arg_name, TypeValidator::class>
   */
  #[NotControllerMethod]
  public function getRequiredParameters(): array {
    return [];
  }

  /**
   * Проверки перед вызовом метода
   * Содержит сырые данные в args
   * @throws PrismaException
   */
  #[NotControllerMethod]
  public function beforeCall(string $methodName, string $httpMethod, array $args, ServerRequestInterface $request, RequestOptions $options): void {

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

  #[NotControllerMethod]
  abstract public function getName(): string;

}