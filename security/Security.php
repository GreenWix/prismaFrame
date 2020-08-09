<?php


namespace GreenWix\prismaFrame\security;


use Psr\Http\Message\ServerRequestInterface;

abstract class Security
{

	/**
	 * @param ServerRequestInterface $request
	 * @return mixed
	 */
	abstract public function beforeRequest(ServerRequestInterface $request): void;

	/**
	 * @param ServerRequestInterface $request
	 * @param string $message
	 */
	abstract public function report(ServerRequestInterface $request, string $message): void;

}