<?php

declare(strict_types=1);


namespace GreenWix\prismaFrame\event\request;


use GreenWix\prismaFrame\controller\Controller;
use GreenWix\prismaFrame\event\Event;
use Psr\Http\Message\ServerRequestInterface;

abstract class RequestEvent extends Event {

	/** @var ServerRequestInterface */
	protected $request;

	public function __construct(ServerRequestInterface $request, string $controller, string $method, array $args){
		$this->request = $request;
	}

}