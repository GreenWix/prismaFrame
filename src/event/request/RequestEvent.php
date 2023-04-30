<?php

declare(strict_types = 1);

namespace GreenWix\prismaFrame\event\request;

use GreenWix\prismaFrame\event\Event;
use GreenWix\prismaFrame\settings\RequestOptions;
use Psr\Http\Message\ServerRequestInterface;

abstract class RequestEvent extends Event {

  protected ServerRequestInterface $request;
  protected string $controller;
  protected string $method;
  protected RequestOptions $options;

  /** @var mixed[] */
  protected array $args;

  /**
   * @param mixed[] $args
   */
  public function __construct(ServerRequestInterface $request, string $controller, string $method, array $args, RequestOptions $options) {
    $this->request = $request;
    $this->controller = $controller;
    $this->method = $method;
    $this->args = $args;
    $this->options = $options;
  }

  public function getRequest(): ServerRequestInterface {
    return $this->request;
  }

  public function getController(): string {
    return $this->controller;
  }

  public function getMethod(): string {
    return $this->method;
  }

  public function getOptions(): RequestOptions {
    return $this->options;
  }

  /**
   * @return mixed[]
   */
  public function getArgs(): array {
    return $this->args;
  }

}