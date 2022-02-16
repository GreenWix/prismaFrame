<?php

declare(strict_types=1);


namespace GreenWix\prismaFrame\security;

//todo отказаться от этого класса в пользу LoggerInterface
abstract class Security {

	abstract public function report(string $message);

}