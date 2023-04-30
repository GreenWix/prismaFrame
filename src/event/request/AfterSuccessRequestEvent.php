<?php

declare(strict_types = 1);

namespace GreenWix\prismaFrame\event\request;

use GreenWix\prismaFrame\Response;
use GreenWix\prismaFrame\settings\RequestOptions;
use Psr\Http\Message\ServerRequestInterface;

class AfterSuccessRequestEvent extends RequestEvent {

  protected Response $response;

  public function __construct(ServerRequestInterface $request, string $controller, string $method, array $args, Response $response, RequestOptions $options) {
    parent::__construct($request, $controller, $method, $args, $options);

    $this->response = $response;
  }

}