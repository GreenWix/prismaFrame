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

}