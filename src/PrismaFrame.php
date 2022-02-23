<?php


namespace GreenWix\prismaFrame;


use GreenWix\prismaFrame\controller\Controller;
use GreenWix\prismaFrame\controller\ControllerManager;
use GreenWix\prismaFrame\error\InternalErrorException;
use GreenWix\prismaFrame\event\EventsHandler;
use GreenWix\prismaFrame\handler\RequestHandler;
use GreenWix\prismaFrame\settings\PrismaFrameSettings;
use GreenWix\prismaFrame\type\TypeManager;
use GreenWix\prismaFrame\type\TypeValidator;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

class PrismaFrame
{

	/** @var bool */
	private $working = false;

	/** @var ControllerManager */
	private $controllerManager;

	/** @var TypeManager */
	private $typeManager;

	/** @var EventsHandler */
	private $eventsHandler;

	/** @var RequestHandler */
	private $requestHandler;

	/** @var PrismaFrameSettings */
	private $settings;

	/** @var LoggerInterface */
	private $logger;

	public function __construct(PrismaFrameSettings $settings, EventsHandler $eventsHandler, LoggerInterface $logger){
		$this->settings = $settings;
		$this->logger = $logger;
		$this->typeManager = new TypeManager();
		$this->controllerManager = new ControllerManager($this);
		$this->requestHandler = new RequestHandler($this);

		$this->eventsHandler = $eventsHandler;
	}

	public function getLogger(): LoggerInterface {
		return $this->logger;
	}

	/**
	 * @param ServerRequestInterface $request
	 * @return Response
	 * @throws InternalErrorException
	 */
	public function handleRequest(ServerRequestInterface $request): Response{
		if(!$this->isWorking()){
			throw new InternalErrorException("Обработка запроса не может быть выполнена, пока PrismaFrame не запущен: PrismaFrame->start()");
		}

		return $this->requestHandler->handle($request);
	}

	public function getEventsHandler(): EventsHandler{
		return $this->eventsHandler;
	}

	/**
	 * @param Controller $controller
	 * @throws GreenWix\prismaFrame\error\InternalErrorException
	 */
	public function addController(Controller $controller): void{
		if($this->isWorking()) {
			throw new InternalErrorException("You cant add new controllers while prismaFrame is working");
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

	public function getTypeManager(): TypeManager {
		return $this->typeManager;
	}

}