<?php


namespace SociallHouse\prismaFrame;


class Response
{

	/** @var array */
	public $response;

	/** @var int */
	public $httpCode;

	public function __construct(array $response, int $httpCode){
		$this->response = $response;
		$this->httpCode = $httpCode;
	}

}