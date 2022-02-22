<?php

declare(strict_types=1);


namespace GreenWix\prismaFrame\event;


use GreenWix\prismaFrame\event\request\AfterRequestEvent;
use GreenWix\prismaFrame\event\request\BeforeRequestEvent;

abstract class EventsHandler {

	abstract public function beforeRequest(BeforeRequestEvent $e);

	abstract public function afterRequest(AfterRequestEvent $e);

}