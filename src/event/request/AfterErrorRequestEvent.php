<?php

namespace GreenWix\prismaFrame\event\request;

use GreenWix\prismaFrame\Response;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class AfterErrorRequestEvent extends RequestEvent {

  protected Response $response;
  protected Throwable $exception;

  public function __construct(ServerRequestInterface $request, string $controller, string $method, array $args, Response $response, Throwable $exception) {
    parent::__construct($request, $controller, $method, $args);

    $this->response = $response;
    $this->exception = $exception;
  }

}