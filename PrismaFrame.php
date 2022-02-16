<?php


namespace GreenWix\prismaFrame;


use GreenWix\prismaFrame\controller\Controller;
use GreenWix\prismaFrame\controller\ControllerManager;
use GreenWix\prismaFrame\error\internal\InternalError;
use GreenWix\prismaFrame\event\EventsHandler;
use GreenWix\prismaFrame\handler\RequestHandler;
use GreenWix\prismaFrame\security\Security;
use GreenWix\prismaFrame\settings\PrismaFrameSettings;
use GreenWix\prismaFrame\type\TypeManager;
use GreenWix\prismaFrame\type\TypeValidator;
use Psr\Http\Message\ServerRequestInterface;

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

	/** @var RequestHandler */
	private $requestHandler;

	public function __construct(PrismaFrameSettings $settings, Security $security, EventsHandler $eventsHandler){
		$this->settings = $settings;
		$this->typeManager = new TypeManager();
		$this->controllerManager = new ControllerManager($this->typeManager);
		$this->requestHandler = new RequestHandler($this);

		$this->security = $security;
		$this->eventsHandler = $eventsHandler;

	}

	/**
	 * @param ServerRequestInterface $request
	 * @return Response
	 * @throws error\internal\InternalErrorException
	 */
	public function handleRequest(ServerRequestInterface $request): Response{
		if(!$this->isWorking()){
			throw InternalError::PRISMAFRAME_IS_NOT_STARTED("Обработка запроса не может быть выполнена, пока PrismaFrame не запущен: PrismaFrame->start()");
		}

		return $this->requestHandler->handle($request);
	}

	public function getEventsHandler(): EventsHandler{
		return $this->eventsHandler;
	}

	public function getSecurity(): Security{
		return $this->security;
	}

	/**
	 * @param Controller $controller
	 * @throws \ReflectionException
	 * @throws error\internal\InternalErrorException
	 */
	public function addController(Controller $controller): void{
		if($this->isWorking()) {
			throw InternalError::PRISMAFRAME_ALREADY_STARTED("You cant add new controllers while prismaFrame is working");
		}

		$this->controllerManager->addController($controller);
	}

	public function addTypeValidator(TypeValidator $validator): void{
		$this->typeManager->addTypeValidator($validator);
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

	public function getControllerManager(): ControllerManager {
		return $this->controllerManager;
	}

}