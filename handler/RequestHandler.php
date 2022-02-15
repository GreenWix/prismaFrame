<?php

declare(strict_types=1);


namespace GreenWix\prismaFrame\handler;


use GreenWix\prismaFrame\error\Error;
use GreenWix\prismaFrame\error\HTTPCodes;
use GreenWix\prismaFrame\error\internal\InternalError;
use GreenWix\prismaFrame\error\internal\InternalErrorException;
use GreenWix\prismaFrame\error\runtime\RuntimeError;
use GreenWix\prismaFrame\error\runtime\RuntimeErrorException;
use GreenWix\prismaFrame\event\request\AfterRequestEvent;
use GreenWix\prismaFrame\event\request\BeforeRequestEvent;
use GreenWix\prismaFrame\PrismaFrame;
use GreenWix\prismaFrame\Response;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class RequestHandler {

	/** @var PrismaFrame */
	protected $prismaFrame;

	public function __construct(PrismaFrame $prismaFrame){
		$this->prismaFrame = $prismaFrame;
	}

	/**
	 * @param ServerRequestInterface $req
	 * @return Response
	 * @throws InternalErrorException
	 */
	public function handle(ServerRequestInterface $req): Response{
		$prismaFrame = $this->prismaFrame;

		if(!$prismaFrame->isWorking()){
			throw InternalError::PRISMAFRAME_IS_NOT_STARTED("Обработка запроса не может быть выполнена, пока PrismaFrame не запущен: PrismaFrame->start()");
		}

		$eventsHandler = $prismaFrame->getEventsHandler();

		try {
			$url = $req->getUri()->getPath();
			$httpMethod = strtoupper($req->getMethod());
			$queryParams = $req->getQueryParams();

			$args = $this->getRequestArgs($req, $httpMethod);

			$this->checkVersion($queryParams);

			[$controller, $method] = $this->getControllerNameAndMethod($url);


			$event = new BeforeRequestEvent();
			$eventsHandler->beforeRequest($event);

			$controllerManager = $prismaFrame->getControllerManager();
			$controller = $controllerManager->getController($controller);
			$response = new Response($controller->callMethod($method, $httpMethod, $args), HTTPCodes::OK);

			return $response;
		}catch(Throwable $e){
			return Error::make($e);
		}finally{
			$event = new AfterRequestEvent();
			$eventsHandler->afterRequest($event);
		}
	}

	private function getRequestArgs(ServerRequestInterface $req, string $httpMethod): array{
		$parsedBody = $req->getParsedBody();
		$queryParams = $req->getQueryParams();

		if(empty($parsedBody)) {
			if($httpMethod === 'GET'){
				$args = $queryParams;
			}else{
				$args = json_decode($req->getBody()->getContents(), true, JSON_THROW_ON_ERROR);
			}
		}else{
			$args = $parsedBody;
		}

		if(!isset($args)){
			throw RuntimeError::BAD_INPUT("Bad input");
		}

		return $args;
	}

	/**
	 * @param array $queryParams
	 * @throws RuntimeErrorException
	 */
	private function checkVersion(array $queryParams): void{
		if (!isset($queryParams["v"])) {
			throw RuntimeError::BAD_INPUT("Parameter \"v\" is required");
		}

		if ($queryParams["v"] !== $this->prismaFrame->getApiVersion()) {
			throw RuntimeError::WRONG_VERSION();
		}
	}

	private function getControllerNameAndMethod(string $url): array{
		$raw = explode("/", $url, 2);
		$raw_2 = explode(".", $raw[1] ?? "", 2);

		$controller = $raw_2[0] ?? "";
		$method = $raw_2[1] ?? "";

		return [$controller, $method];
	}

}