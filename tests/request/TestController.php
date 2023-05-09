<?php

namespace GreenWix\prismaFrame\tests\request;

use GreenWix\prismaFrame\controller\ControllerBase;
use GreenWix\prismaFrame\controller\HttpMethod;
use GreenWix\prismaFrame\controller\NotControllerMethod;
use GreenWix\prismaFrame\type\validators\exception\BadValidationException;

class TestController extends ControllerBase {

  #[NotControllerMethod]
  public function getName(): string {
    return "test";
  }

  #[NotControllerMethod]
  public function getRequiredParameters(): array {
    return [
      'test_number' => 'int'
    ];
  }

  #[HttpMethod(HttpMethod::GET)]
  public function doSomething(string $value, int $test_number, bool $optional_value = true): array {
    return [
      'value' => $value,
      'optional_value' => $optional_value
    ];
  }

  #[HttpMethod(HttpMethod::POST)]
  public function doSomethingPost(string $value, int $test_number, bool $optional_value = true): array {
    return [
      'value' => $value,
      'optional_value' => $optional_value
    ];
  }

  #[HttpMethod(HttpMethod::GET | HttpMethod::POST)]
  public function doSomethingException(int $test_number): array {
    throw new BadValidationException("Something is wrong");
  }

}