<?php

namespace GreenWix\prismaFrame;

class Response {

  /** @var mixed[] */
  public array $response;

  public int $httpCode;

  public function __construct(array $response, int $httpCode) {
    $this->response = $response;
    $this->httpCode = $httpCode;
  }

}