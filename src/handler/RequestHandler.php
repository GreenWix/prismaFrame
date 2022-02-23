<?php

declare(strict_types=1);


namespace GreenWix\prismaFrame\handler;


use GreenWix\prismaFrame\controller\exception\BadInputException;
use GreenWix\prismaFrame\error\Error;
use GreenWix\prismaFrame\error\HTTPCodes;
use GreenWix\prismaFrame\error\RuntimeError;
use GreenWix\prismaFrame\event\request\AfterRequestEvent;
use GreenWix\prismaFrame\event\request\BeforeRequestEvent;
use GreenWix\prismaFrame\handler\exception\VersionException;
use GreenWix\prismaFrame\PrismaFrame;
use GreenWix\prismaFrame\Response;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class RequestHandler {

	/** @var PrismaFrame */
	protected $prismaFrame;

	public function __construct(PrismaFrame $prismaFrame) {
		$this->prismaFrame = $prismaFrame;
	}

	/**
	 * @param ServerRequestInterface $request
	 * @return Response
	 */
	public function handle(ServerRequestInterface $request): Response {
		$prismaFrame = $this->prismaFrame;

		$eventsHandler = $prismaFrame->getEventsHandler();

		try {
			$url = $request->getUri()->getPath();
			$httpMethod = strtoupper($request->getMethod());
			$queryParams = $request->getQueryParams();

			$args = $this->getRequestArgs($request, $httpMethod);

			$this->checkVersion($queryParams);

			[$controller, $method] = $this->getControllerNameAndMethod($url);

			$event = new BeforeRequestEvent($request, $controller, $method, $args);
			$eventsHandler->beforeRequest($event);

			$controllerManager = $prismaFrame->getControllerManager();
			$controller = $controllerManager->getController($controller);

			return new Response($controller->callMethod($method, $httpMethod, $args), HTTPCodes::OK);
		} catch (Throwable $e) {
			return Error::make($prismaFrame, $e);
		} finally {
			if (isset($controller, $method, $args, $response)) {
				$event = new AfterRequestEvent($request, $controller, $method, $args, $response);
				$eventsHandler->afterRequest($event);
			}
		}
	}

	/**
	 * @param ServerRequestInterface $req
	 * @param string $httpMethod
	 * @return array
	 * @throws BadInputException
	 */
	private function getRequestArgs(ServerRequestInterface $req, string $httpMethod): array {
		$parsedBody = $req->getParsedBody();
		$queryParams = $req->getQueryParams();

		//todo более корректно отрабатывать момент, когда мы льем файл
		if (empty($parsedBody)) {
			if ($httpMethod === 'GET') {
				$args = $queryParams;
			} else {
				$args = json_decode($req->getBody()->getContents(), true, JSON_THROW_ON_ERROR);
			}
		} else {
			$args = $parsedBody;
		}

		if (!isset($args)) {
			throw new BadInputException("Couldn't get args");
		}

		return $args;
	}

	/**
	 * @param array $queryParams
	 * @throws VersionException
	 */
	private function checkVersion(array $queryParams): void {
		if (!isset($queryParams["v"])) {
			throw new VersionException("Parameter \"v\" is required");
		}

		if ($queryParams["v"] !== $this->prismaFrame->getApiVersion()) {
			throw new VersionException("This version is incompatible");
		}
	}

	private function getControllerNameAndMethod(string $url): array {
		$hostAndControllerAndMethod = explode("/", $url, 2);

		$controllerAndMethod = $hostAndControllerAndMethod[1];
		$controllerAndMethodArray = explode(".", $controllerAndMethod ?? "", 2);

		$controller = $controllerAndMethodArray[0] ?? "";
		$method = $controllerAndMethodArray[1] ?? "";

		return [$controller, $method];
	}

}