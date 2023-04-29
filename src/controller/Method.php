<?php

namespace GreenWix\prismaFrame\controller;

use GreenWix\prismaFrame\controller\exception\BadInputException;
use GreenWix\prismaFrame\controller\exception\WrongHttpMethodException;
use GreenWix\prismaFrame\type\TypeManagerException;
use GreenWix\prismaFrame\type\validators\exception\BadValidationException;

final class Method {

  public string $name;

  /** @var MethodParameter[] */
  private array $parameters;

  /** @var bool[] array<string, bool> */
  private array $httpMethods = [];

  // Используется для вывода об ошибке, которая появляется если запрос сделан с неподдерживаемым HTTP методом
  // Нужен для того, чтобы постоянно implode("|", httpMethods) не делать
  private string $flatHttpMethods;

  private Controller $controller;

  /**
   * Method constructor.
   * @param MethodParameter[] $parameters
   * @param string[]          $httpMethods
   */
  public function __construct(string $name, array $parameters, array $httpMethods, Controller $controller) {
    $this->name = $name;
    $this->parameters = $parameters;
    foreach ($httpMethods as $method) {
      $this->httpMethods[$method] = true; // чтобы можно было потом ускоренно проверять через isset
    }
    $this->flatHttpMethods = implode("|", $httpMethods);
    $this->controller = $controller;
  }

  /**
   * @param mixed[] $args
   * @return mixed[]
   *
   * @throws BadInputException
   * @throws TypeManagerException
   * @throws WrongHttpMethodException
   * @throws BadValidationException
   */
  public function invoke(string $httpMethod, array $args): array {
    if (!isset($this->httpMethods[strtoupper($httpMethod)])) {
      throw new WrongHttpMethodException("This method supports only $this->flatHttpMethods HTTP method(s). Got $httpMethod");
    }

    $values = [];
    foreach ($this->parameters as $name => $param) {
      try {
        if ($param->required && !isset($args[$name])) {
          throw new BadInputException("Parameter \"$name\" is required");
        }

        if (!isset($args[$name])) {
          continue;
        }

        $argValue = $args[$name];
        $values[] = $param->validateAndGetValue($argValue);
      } catch (BadValidationException $exception) {
        $value = "Passed wrong value to \"$name\" parameter";
        $message = $exception->getMessage();

        if ($message !== BadValidationException::DEFAULT_MESSAGE) {
          $value .= ": {$message}";
        }

        throw new BadValidationException($value, $exception->httpCode, $exception);
     }
    }

    return $this->controller->{$this->name}(...$values);
  }

}