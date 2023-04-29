<?php

namespace GreenWix\prismaFrame\tests\request;

use GreenWix\prismaFrame\PrismaFrame;
use GreenWix\prismaFrame\Response;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\Diactoros\StreamFactory;
use Laminas\Diactoros\Uri;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamFactoryInterface;

abstract class RequestsTestCase extends TestCase {

  protected PrismaFrame $prismaFrame;

  protected function get(string $controllerAndMethod, array $query, ?array $body = null, array $serverParams = []): Response {
    return $this->_makeRequest('GET', $controllerAndMethod, $query, $body, $serverParams);
  }

  protected function post(string $controllerAndMethod, array $query, array $body, array $serverParams = []): Response {
    return $this->_makeRequest('POST', $controllerAndMethod, $query, $body, $serverParams);
  }

  protected function put(string $controllerAndMethod, array $query, ?array $body = null, array $serverParams = []): Response {
    return $this->_makeRequest('PUT', $controllerAndMethod, $query, $body, $serverParams);
  }

  protected function patch(string $controllerAndMethod, array $query, ?array $body = null, array $serverParams = []): Response {
    return $this->_makeRequest('PATCH', $controllerAndMethod, $query, $body, $serverParams);
  }

  protected function assertSuccessResponse(Response $response, ?array $expectedResponse = null): void {
    $this->assertFalse($response->isError(), 'Response is not successful: ' . $response->getErrorMessage());

    if (isset($expectedResponse)) {
      $this->assertEquals($response->response, $expectedResponse);
    }
  }

  protected function assertErrorResponse(Response $response, string $expectedMessage = '', int $expectedCode = 0): void {
    $this->assertTrue($response->isError(), 'Response is not error: ' . json_encode($response->response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    if (!empty($expectedMessage)) {
      $this->assertEquals($response->getErrorMessage(), $expectedMessage);
    }

    if ($expectedCode > 0) {
      $this->assertEquals($response->getErrorCode(), $expectedCode);
    }
  }

  private function _makeRequest(string $method, string $controllerAndMethod, array $query, ?array $body = null, array $serverParams = []): Response {
    $uri = new Uri("https://example.com/{$controllerAndMethod}");

    if (!isset($serverParams['REMOTE_ADDR'])) {
      $serverParams['REMOTE_ADDR'] = '1.1.1.1';
    }

    $request = ServerRequestFactory::fromGlobals($serverParams)->withUri($uri)->withQueryParams($query)->withMethod($method);
    if (isset($body)) {
      $factory = new StreamFactory();
      $encodedBody = json_encode($body);

      $request = $request->withBody($factory->createStream($encodedBody));
    }

    return $this->prismaFrame->handleRequest($request);
  }

}