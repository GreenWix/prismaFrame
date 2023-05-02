<?php

namespace GreenWix\prismaFrame\controller;

use Attribute;
use JetBrains\PhpStorm\ExpectedValues;

#[Attribute(Attribute::TARGET_METHOD)]
class HttpMethod {

  public const GET   = 1;
  public const POST  = 2;
  public const PUT   = 4;
  public const PATCH = 8;

  public int $methods;

  public function __construct(#[ExpectedValues(flagsFromClass: HttpMethod::class)] int $methods) {
    $this->methods = $methods;
  }

  /**
   * @return string[]
   */
  public function toStringArray(): array {
    $result = [];

    $this->append($result, self::GET, 'GET');
    $this->append($result, self::POST, 'POST');
    $this->append($result, self::PUT, 'PUT');
    $this->append($result, self::PATCH, 'PATCH');

    return $result;
  }

  /**
   * @param string[] $result
   */
  private function append(array &$result, int $bit, string $value): void {
    if (($this->methods & $bit) !== 0) {
      $result[] = $value;
    }
  }

}