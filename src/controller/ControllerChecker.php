<?php

namespace GreenWix\prismaFrame\controller;

use GreenWix\prismaFrame\error\HTTPCodes;
use GreenWix\prismaFrame\error\InternalErrorException;
use GreenWix\prismaFrame\PrismaFrame;
use ReflectionClass;
use ReflectionMethod;
use Throwable;

class ControllerChecker {

  protected PrismaFrame $prismaFrame;

  public function __construct(PrismaFrame $prismaFrame) {
    $this->prismaFrame = $prismaFrame;
  }

  /**
   * @return Method[]
   * @throws InternalErrorException
   */
  public function getControllerMethods(Controller $controller): array {
    $resultMethods = [];
    $controllerClass = new ReflectionClass($controller);
    $controllerName = $controller->getName();

    $methods = $controllerClass->getMethods(ReflectionMethod::IS_PUBLIC);
    foreach ($methods as $method) {
      $methodName = $method->getName();
      if ($this->isMethodInternal($methodName)) {
        continue;
      }

      $controllerAndMethodName = "$controllerName.$methodName";

      try {
        $resultMethods[$methodName] = $this->checkAndGetMethod($method, $controller);
      } catch (\Exception $exception) {
        throw new InternalErrorException("An error occurred while processing $controllerAndMethodName method", HTTPCodes::INTERNAL_SERVER_ERROR, $exception);
      }
    }
    return $resultMethods;
  }

  /**
   * @throws InternalErrorException
   * @throws InternalErrorException
   */
  protected function checkAndGetMethod(ReflectionMethod $method, Controller $controller): Method {
    $methodName = $method->getName();

    $this->checkReturnType($method);

    $attrs = $method->getAttributes(HttpMethod::class);
    if (count($attrs) === 0) {
      throw new InternalErrorException("You must specify #[HttpMethod] attribute");
    }

    if (count($attrs) > 1) {
      throw new InternalErrorException("Multiple #[HttpMethod] attribute is not allowed. Use |");
    }

    [$attr] = $attrs;
    /** @var HttpMethod $httpMethodAttr */
    $httpMethodAttr = $attr->newInstance();

    $httpMethods = $httpMethodAttr->toStringArray();

    foreach ($httpMethods as $httpMethod) {
      if (!$this->isHttpMethodAllowed($httpMethod)) {
        throw new InternalErrorException("HTTP method $httpMethod is not supported");
      }
    }

    $parameters = $this->checkAndGetParameters($method);

    return new Method($methodName, $parameters, $httpMethods, $controller);
  }

  /**
   * @return MethodParameter[] array<string, MethodParameter>
   *
   * @throws InternalErrorException
   */
  protected function checkAndGetParameters(ReflectionMethod $method): array {
    $resultParameters = [];

    $i = 0;
    foreach ($method->getParameters() as $methodParameter) {
      $type = $methodParameter->getType();
      if ($type->allowsNull()) {
        throw new InternalErrorException("Nullable types are not supported");
      }

      $parameterName = $methodParameter->getName();

      $extraData = [];
      $parameter = new MethodParameter(
        $this->prismaFrame->getTypeManager(),
        $parameterName,
        $type->getName(),
        $extraData,
        !$methodParameter->isOptional()
      );

      $resultParameters[$parameterName] = $parameter;
      $this->checkParameterType($parameter);

      ++$i;
    }

    return $resultParameters;
  }

  /**
   * @throws InternalErrorException
   */
  protected function checkParameterType(MethodParameter $docParameter): void {
    $parameterTypeName = $docParameter->typeName;
    $parameterName = $docParameter->name;

    $typeManager = $this->prismaFrame->getTypeManager();

    if (!$typeManager->hasTypeValidator($parameterTypeName)) {
      throw new InternalErrorException("Type $parameterTypeName of $parameterName argument is not supported");
    }
  }

  /**
   * @throws InternalErrorException
   */
  protected function checkReturnType(ReflectionMethod $method): void {
    $returnType = $method->getReturnType();

    $requiredReturnType = 'array';

    if ($returnType === null) {
      throw new InternalErrorException("Method returns void instead of $requiredReturnType");
    }

    $actualReturnTypeName = $returnType->getName();
    if ($actualReturnTypeName !== $requiredReturnType) {
      throw new InternalErrorException("Method returns $actualReturnTypeName instead of $requiredReturnType");
    }
  }

  public function isMethodInternal(string $methodName): bool {
    $internalMethods = ['getName', 'callMethod'];

    return in_array($methodName, $internalMethods, true);
  }

  public function isHttpMethodAllowed(string $httpMethod): bool {
    $allowedHttpMethods = ['GET', 'POST', 'PATCH', 'PUT'];

    return in_array($httpMethod, $allowedHttpMethods, true);
  }

}