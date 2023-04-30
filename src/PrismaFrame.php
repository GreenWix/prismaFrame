<?php

namespace GreenWix\prismaFrame;

use GreenWix\prismaFrame\controller\Controller;
use GreenWix\prismaFrame\controller\ControllerManager;
use GreenWix\prismaFrame\error\Error;
use GreenWix\prismaFrame\error\InternalErrorException;
use GreenWix\prismaFrame\event\EventsHandler;
use GreenWix\prismaFrame\handler\RequestHandler;
use GreenWix\prismaFrame\settings\PrismaFrameSettings;
use GreenWix\prismaFrame\settings\RequestOptions;
use GreenWix\prismaFrame\type\TypeManager;
use GreenWix\prismaFrame\type\TypeValidator;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

class PrismaFrame {

  private bool $working = false;
  private ControllerManager $controllerManager;
  private TypeManager $typeManager;
  private EventsHandler $eventsHandler;
  private RequestHandler $requestHandler;
  private PrismaFrameSettings $settings;
  private LoggerInterface $logger;

  public function __construct(PrismaFrameSettings $settings, EventsHandler $eventsHandler, LoggerInterface $logger) {
    $this->settings = $settings;
    $this->logger = $logger;
    $this->typeManager = new TypeManager();
    $this->controllerManager = new ControllerManager($this);
    $this->requestHandler = new RequestHandler($this);

    $this->eventsHandler = $eventsHandler;
  }

  public function getLogger(): LoggerInterface {
    return $this->logger;
  }

  /**
   * @throws InternalErrorException
   */
  public function handleRequest(ServerRequestInterface $request, ?RequestOptions $options = null): Response {
    if (!$this->isWorking()) {
      throw new InternalErrorException("Start prismaFrame before handling requests");
    }

    if ($options === null) {
      $options = RequestOptions::new();
    }

    return $this->requestHandler->handle($request, $options);
  }

  public function getEventsHandler(): EventsHandler {
    return $this->eventsHandler;
  }

  /**
   * @throws InternalErrorException
   */
  public function addController(Controller $controller): void {
    if ($this->isWorking()) {
      throw new InternalErrorException("You can't add new controllers while prismaFrame is working");
    }

    $this->controllerManager->addController($controller);
  }

  /**
   * @throws type\TypeManagerException
   */
  public function addTypeValidator(TypeValidator $validator): void {
    $this->typeManager->addTypeValidator($validator);
  }

  public function isDebug(): bool {
    return $this->settings->debug;
  }

  /**
   * @return string[]
   */
  public function getSupportedApiVersions(): array {
    return $this->settings->supportedApiVersions;
  }

  public function getSettings(): PrismaFrameSettings {
    return $this->settings;
  }

  public function start(): void {
    $this->working = true;
  }

  public function isWorking(): bool {
    return $this->working;
  }

  public function getControllerManager(): ControllerManager {
    return $this->controllerManager;
  }

  public function getTypeManager(): TypeManager {
    return $this->typeManager;
  }

}