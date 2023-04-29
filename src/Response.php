<?php

namespace GreenWix\prismaFrame;

use GreenWix\prismaFrame\error\HTTPCodes;

class Response {

  /** @var mixed[] */
  public array $response;

  public int $httpCode;

  public function __construct(array $response, int $httpCode) {
    $this->response = $response;
    $this->httpCode = $httpCode;
  }

  public function isError(): bool {
    return $this->httpCode >= HTTPCodes::BAD_REQUEST;
  }

  public function getErrorMessage(): string {
    return $this->response['error']['message'] ?? '<no error message>';
  }

  public function getErrorCode(): int {
    return $this->response['error']['code'] ?? 0;
  }

}