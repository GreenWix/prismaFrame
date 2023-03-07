<?php

declare(strict_types = 1);

namespace GreenWix\prismaFrame\controller;

use GreenWix\prismaFrame\controller\exception\UnknownControllerException;
use GreenWix\prismaFrame\error\InternalErrorException;
use GreenWix\prismaFrame\PrismaFrame;

final class ControllerManager {

  /** @var Controller[] */
  private array $controllers = [];

  private ControllerChecker $checker;

  public function __construct(PrismaFrame $prismaFrame) {
    $this->checker = new ControllerChecker($prismaFrame);
  }

  /**
   * @throws UnknownControllerException
   */
  public function getController(string $name): Controller {
    if (!isset($this->controllers[$name])) {
      throw new UnknownControllerException("Unknown controller");
    }

    return $this->controllers[$name];
  }

  /**
   * @throws InternalErrorException
   */
  public function addController(Controller $controller): void {
    $controllerName = $controller->getName();
    if (isset($this->controllers[$controllerName])) {
      throw new InternalErrorException("Controller $controllerName is already registered");
    }

    $controller->methods = $this->checker->getControllerMethods($controller);

    $this->controllers[$controllerName] = $controller;
  }

}