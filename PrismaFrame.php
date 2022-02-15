<?php


namespace GreenWix\prismaFrame;


use GreenWix\prismaFrame\controller\ControllerManager;
use GreenWix\prismaFrame\event\EventsHandler;
use GreenWix\prismaFrame\security\Security;
use GreenWix\prismaFrame\settings\PrismaFrameSettings;
use GreenWix\prismaFrame\type\TypeManager;

class PrismaFrame
{

	/** @var bool */
	private $working = false;

	/** @var PrismaFrameSettings */
	private $settings;

	/** @var ControllerManager */
	private $controllerManager;

	/** @var TypeManager */
	private $typeManager;

	/** @var Security */
	private $security;

	/** @var EventsHandler */
	private $eventsHandler;

	public function __construct(PrismaFrameSettings $settings, Security $security, EventsHandler $handler){
		$this->settings = $settings;
		$this->controllerManager = new ControllerManager($this);
		$this->typeManager = new TypeManager();

		$this->security = $security;
		$this->eventsHandler = $handler;
	}

	public function getEventsHandler(): EventsHandler{
		return $this->eventsHandler;
	}

	public function getControllerManager(): ControllerManager{
		return $this->controllerManager;
	}

	public function getSecurity(): Security{
		return $this->security;
	}

	public function getTypeManager(): TypeManager{
		return $this->typeManager;
	}

	public function isDebug(): bool{
		return $this->settings->debug;
	}

	public function getApiVersion(): string{
		return $this->settings->apiVersion;
	}

	public function getSettings(): PrismaFrameSettings{
		return $this->settings;
	}

	public function start(){
		$this->working = true;
	}

	public function isWorking(): bool{
		return $this->working;
	}

}