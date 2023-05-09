<?php

namespace GreenWix\prismaFrame\tests\request;

use GreenWix\prismaFrame\error\InternalErrorException;
use GreenWix\prismaFrame\PrismaFrame;
use GreenWix\prismaFrame\settings\PrismaFrameSettings;
use Laminas\Diactoros\ServerRequestFactory;
use Psr\Log\NullLogger;

class RequestsTest extends RequestsTestCase {

  protected ServerRequestFactory $serverRequestFactory;

  /**
   * @throws InternalErrorException
   */
  protected function setUp(): void {
    $settings = PrismaFrameSettings::new(['1.0.0', '1.0.1'])->withDebug();

    $eventsHandler = new TestEventsHandler();
    $logger = new NullLogger();

    $this->serverRequestFactory = new ServerRequestFactory();

    $this->prismaFrame = PrismaFrame::new($settings, $eventsHandler, $logger)
      ->addController(new TestController())
      ->start();
  }

  public function testRequestSuccess(): void {
    $params = [
      'v' => '1.0.0',
      'value' => 'test',
      'test_number' => 6
    ];

    $response = $this->get('test.doSomething', $params);
    $this->assertSuccessResponse(
      $response,
      [
        'value' => 'test',
        'optional_value' => true,
      ]
    );
  }

  public function testRequestCustomExceptions(): void {
    $params = [
      'v' => '1.0.0',
      'test_number' => 6
    ];

    $response = $this->get('test.doSomethingException', $params);
    $this->assertErrorResponse($response, 'Something is wrong');
  }

  public function testRequestNoParams(): void {
    $params = [
      'v' => '1.0.0',
    ];

    $response = $this->get('test.doSomething', $params);
    $this->assertErrorResponse($response, 'Parameter value is required');
  }

  public function testRequestNoVersion(): void {
    $params = [
      'value' => 'test',
    ];

    $response = $this->get('test.doSomething', $params);
    $this->assertErrorResponse($response, 'Parameter v is required');
  }

  public function testRequestUnsupportedVersion(): void {
    $params = [
      'v' => '0.9',
      'value' => 'test',
    ];

    $response = $this->get('test.doSomething', $params);
    $this->assertErrorResponse($response, 'This version is unsupported');
  }

  public function testRequestWrongMethod(): void {
    $query = [
      'v' => '1.0.0',
    ];

    $params = [
      'value' => 'test',
    ];

    $response = $this->post('test.doSomething', $query, $params);
    $this->assertErrorResponse($response, 'This method supports only GET HTTP method(s). Got POST');
  }

  public function testRequestPostMethod(): void {
    $query = [
      'v' => '1.0.0',
    ];

    $params = [
      'value' => 'test',
      'test_number' => 6
    ];

    $response = $this->post('test.doSomethingPost', $query, $params);
    $this->assertSuccessResponse(
      $response,
      [
        'value' => 'test',
        'optional_value' => true
      ]
    );
  }

  public function testRequestValidators(): void {
    $query = [
      'v' => '1.0.0',
    ];

    $params = [
      'value' => 'test',
      'optional_value' => 'test',
      'test_number' => 6
    ];

    $response = $this->post('test.doSomethingPost', $query, $params);
    $this->assertErrorResponse($response, 'Passed wrong value to "optional_value" parameter');
  }

}