<?php

declare(strict_types = 1);

namespace GreenWix\prismaFrame\handler;

use GreenWix\prismaFrame\controller\exception\BadInputException;
use GreenWix\prismaFrame\error\Error;
use GreenWix\prismaFrame\error\HTTPCodes;
use GreenWix\prismaFrame\event\request\AfterErrorRequestEvent;
use GreenWix\prismaFrame\event\request\AfterSuccessRequestEvent;
use GreenWix\prismaFrame\event\request\BeforeRequestEvent;
use GreenWix\prismaFrame\handler\exception\VersionException;
use GreenWix\prismaFrame\PrismaFrame;
use GreenWix\prismaFrame\Response;
use GreenWix\prismaFrame\settings\RequestOptions;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class RequestHandler {

  protected PrismaFrame $prismaFrame;
  protected RequestOptions $options;

  public function __construct(PrismaFrame $prismaFrame) {
    $this->prismaFrame = $prismaFrame;
  }

  public function handle(ServerRequestInterface $request, RequestOptions $options): Response {
    $this->options = $options;
    $prismaFrame = $this->prismaFrame;
    $eventsHandler = $prismaFrame->getEventsHandler();

    try {
      $url = $request->getUri()->getPath();
      $httpMethod = strtoupper($request->getMethod());
      $queryParams = $request->getQueryParams();

      $args = $this->getRequestArgs($request, $httpMethod);

      $this->checkVersion($queryParams);

      [$controller, $method] = $this->getControllerNameAndMethod($url);

      $event = new BeforeRequestEvent($request, $controller, $method, $args, $options);
      $eventsHandler->beforeRequest($event);

      $controllerManager = $prismaFrame->getControllerManager();
      $controller = $controllerManager->getController($controller);

      $response = new Response($controller->callMethod($method, $httpMethod, $args), HTTPCodes::OK);

      return $response;
    } catch (Throwable $exception) {
      $errorResponse = Error::make($prismaFrame->isDebug(), $exception);

      $controllerName = isset($controller) ? $controller->getName() : "<no controller>";

      $event = new AfterErrorRequestEvent(
        $request,
        $controllerName,
        $method ?? "<no method>",
        $args ?? [],
        $errorResponse,
        $exception,
        $options
      );
      $eventsHandler->afterErrorRequest($event);

      return $errorResponse;
    } finally {
      if (isset($controller, $method, $args, $response)) {
        $event = new AfterSuccessRequestEvent($request, $controller->getName(), $method, $args, $response, $options);
        $eventsHandler->afterSuccessfulRequest($event);
      }
    }
  }

  /**
   * @return mixed[]
   * @throws BadInputException
   */
  private function getRequestArgs(ServerRequestInterface $req, string $httpMethod): array {
    $parsedBody = $req->getParsedBody();
    $queryParams = $req->getQueryParams();

    //todo более корректно отрабатывать момент, когда мы льем файл
    if (empty($parsedBody)) {
      if ($httpMethod === 'GET') {
        $args = $queryParams;
      } else {
        $args = json_decode($req->getBody()->getContents(), true, JSON_THROW_ON_ERROR);
      }
    } else {
      $args = $parsedBody;
    }

    if (!isset($args)) {
      throw new BadInputException("Couldn't get args");
    }

    return $args;
  }

  /**
   * @param mixed[] $queryParams
   * @throws VersionException
   */
  private function checkVersion(array $queryParams): void {
    if (!isset($queryParams["v"])) {
      throw new VersionException("Parameter v is required");
    }

    $version = $queryParams["v"];
    $supportedVersions = $this->prismaFrame->getSupportedApiVersions();
    if (!in_array($version, $supportedVersions, true)) {
      throw new VersionException("This version is unsupported");
    }
  }

  /**
   * @return string[]
   */
  private function getControllerNameAndMethod(string $url): array {
    $opts = $this->options;

    if ($opts->isForcedControllerAndMethod()) {
      $controller = $opts->force_controller;
      $method = $opts->force_method;
    } else {
      $hostAndControllerAndMethod = explode("/", $url, 2);

      $controllerAndMethod = $hostAndControllerAndMethod[1];
      $controllerAndMethodArray = explode(".", $controllerAndMethod ?? "", 2);

      $controller = $controllerAndMethodArray[0] ?? "";
      $method = $controllerAndMethodArray[1] ?? "";
    }

    return [$controller, $method];
  }

}