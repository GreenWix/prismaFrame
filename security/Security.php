<?php

declare(strict_types=1);


namespace GreenWix\prismaFrame\security;


abstract class Security {

	abstract public function report(string $message);

}