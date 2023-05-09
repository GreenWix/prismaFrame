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
  public function getControllerMethods(ControllerBase $controller): array {
    $resultMethods = [];
    $controllerClass = new ReflectionClass($controller);
    $controllerName = $controller->getName();

    $methods = $controllerClass->getMethods(ReflectionMethod::IS_PUBLIC);
    foreach ($methods as $method) {
      $methodName = $method->getName();
      if ($this->isMethodInternal($method)) {
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
  protected function checkAndGetMethod(ReflectionMethod $method, ControllerBase $controller): Method {
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

    $parameters = $this->checkAndGetParameters($method, $controller);

    return new Method($methodName, $parameters, $httpMethods, $controller);
  }

  /**
   * @return MethodParameter[] array<string, MethodParameter>
   *
   * @throws InternalErrorException
   */
  protected function checkAndGetParameters(ReflectionMethod $method, ControllerBase $controller): array {
    $resultParameters = [];
    $methodName = $method->getName();

    foreach ($method->getParameters() as $methodParameter) {
      $type = $methodParameter->getType();
      if ($type->allowsNull()) {
        throw new InternalErrorException("Nullable types are not supported");
      }

      $parameterName = $methodParameter->getName();

      $parameter = new MethodParameter(
        $this->prismaFrame->getTypeManager(),
        $parameterName,
        $type->getName(),
        $methodParameter->getAttributes(),
        !$methodParameter->isOptional()
      );

      $resultParameters[$parameterName] = $parameter;
      $this->checkParameterType($parameter);
    }

    foreach ($controller->getRequiredParameters() as $parameterName => $typeName) {
      if (!isset($resultParameters[$parameterName])) {
        throw new InternalErrorException("Method $methodName has no $parameterName parameter which is required");
      }

      $parameter = $resultParameters[$parameterName];
      if ($parameter->typeName !== $typeName) {
        throw new InternalErrorException("Method $methodName has required parameter $parameterName with wrong type");
      }

      if (!$parameter->required) {
        throw new InternalErrorException("Method $methodName has required parameter $parameterName which is optional in method parameters");
      }
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

  public function isMethodInternal(ReflectionMethod $method): bool {
    $attrs = $method->getAttributes(NotControllerMethod::class);

    return !empty($attrs);
  }

  public function isHttpMethodAllowed(string $httpMethod): bool {
    $allowedHttpMethods = ['GET', 'POST', 'PATCH', 'PUT'];

    return in_array($httpMethod, $allowedHttpMethods, true);
  }

}