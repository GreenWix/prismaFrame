<?php

namespace GreenWix\prismaFrame\tests\request;

use GreenWix\prismaFrame\event\request\AfterErrorRequestEvent;
use GreenWix\prismaFrame\event\request\AfterSuccessRequestEvent;
use GreenWix\prismaFrame\event\request\BeforeRequestEvent;

class TestEventsHandler extends \GreenWix\prismaFrame\event\EventsHandler {

  public function beforeRequest(BeforeRequestEvent $event): void {

  }

  public function afterSuccessfulRequest(AfterSuccessRequestEvent $event): void {

  }

  public function afterErrorRequest(AfterErrorRequestEvent $event): void {

  }
}