<?php

namespace GreenWix\prismaFrame\tests\request;

use GreenWix\prismaFrame\controller\Controller;
use GreenWix\prismaFrame\controller\HttpMethod;
use GreenWix\prismaFrame\type\validators\exception\BadValidationException;

class TestController extends Controller {

  public function getName(): string {
    return "test";
  }

  #[HttpMethod(HttpMethod::GET)]
  public function doSomething(string $value, bool $optional_value = true): array {
    return [
      'value' => $value,
      'optional_value' => $optional_value
    ];
  }

  #[HttpMethod(HttpMethod::POST)]
  public function doSomethingPost(string $value, bool $optional_value = true): array {
    return [
      'value' => $value,
      'optional_value' => $optional_value
    ];
  }

  #[HttpMethod(HttpMethod::GET | HttpMethod::POST)]
  public function doSomethingException(): array {
    throw new BadValidationException("Something is wrong");
  }

}