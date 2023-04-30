<?php

namespace GreenWix\prismaFrame\settings;

class RequestOptions {

  private static ?RequestOptions $cachedOptions = null;

  public string $force_controller;
  public string $force_method;

  public static function new(): self {
    if (self::$cachedOptions === null) {
      self::$cachedOptions = new self();
    }

    self::$cachedOptions->reset();
    return self::$cachedOptions;
  }

  public function reset(): void {
    $this->force_controller = '';
    $this->force_method = '';
  }

  public function forceControllerAndMethod(string $controller, string $method): self {
    $this->force_controller = $controller;
    $this->force_method = $method;

    return $this;
  }

  public function isForcedControllerAndMethod(): bool {
    return !empty($this->force_controller) && !empty($this->force_method);
  }

  private function __construct() {
  }

}