<?php

namespace GreenWix\prismaFrame;

use GreenWix\prismaFrame\controller\ControllerBase;
use GreenWix\prismaFrame\controller\ControllerManager;
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

  public static function new(PrismaFrameSettings $settings, EventsHandler $eventsHandler, LoggerInterface $logger): self {
    return new self($settings, $eventsHandler, $logger);
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
  public function addController(ControllerBase $controller): self {
    if ($this->isWorking()) {
      throw new InternalErrorException("You can't add new controllers while prismaFrame is working");
    }

    $this->controllerManager->addController($controller);

    return $this;
  }

  /**
   * @throws type\TypeManagerException
   */
  public function addTypeValidator(TypeValidator $validator): self {
    $this->typeManager->addTypeValidator($validator);

    return $this;
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

  public function start(): self {
    $this->working = true;

    return $this;
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

  private function __construct(PrismaFrameSettings $settings, EventsHandler $eventsHandler, LoggerInterface $logger) {
    $this->settings = $settings;
    $this->logger = $logger;
    $this->typeManager = new TypeManager();
    $this->controllerManager = new ControllerManager($this);
    $this->requestHandler = new RequestHandler($this);

    $this->eventsHandler = $eventsHandler;
  }

}