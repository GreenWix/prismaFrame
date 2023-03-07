<?php

declare(strict_types = 1);

namespace GreenWix\prismaFrame\event;

use GreenWix\prismaFrame\event\request\AfterErrorRequestEvent;
use GreenWix\prismaFrame\event\request\AfterSuccessRequestEvent;
use GreenWix\prismaFrame\event\request\BeforeRequestEvent;

abstract class EventsHandler {

  /**
   * Событие перед обрбаоткой запроса
   * Для отмены обработки запроса используйте эксепшены
   */
  abstract public function beforeRequest(BeforeRequestEvent $event): void;

  /**
   * Событие после успешной обработки запроса
   */
  abstract public function afterSuccessfulRequest(AfterSuccessRequestEvent $event): void;

  /**
   * Событие после провальной обработки запроса (вылез эксепшен)
   */
  abstract public function afterErrorRequest(AfterErrorRequestEvent $event): void;

}