<?php


namespace SociallHouse\prismaFrame\settings;


final class PrismaFrameSettings
{
	public function __construct(bool $debug, string $apiVersion){
		$this->debug = $debug;
		$this->apiVersion = $apiVersion;
	}

	/** @var bool */
	public $debug;

	/** @var string */
	public $apiVersion;

}