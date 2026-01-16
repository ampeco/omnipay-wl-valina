<?php

declare(strict_types=1);

namespace Ampeco\OmnipayWlValina\Message;

use Illuminate\Support\Facades\Log;
use Omnipay\Common\Http\ClientInterface;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Tests\TestCase;
use Mockery;

class NotificationRequestTest extends TestCase
{
    private static string $mockedResponseBody = '';
    private static int $mockedStatusCode = 200;
    private static string $mockedContentType = 'application/json';

    private NotificationRequest $request;

    public static function setMockedCurlResponse(string $body, int $statusCode, string $contentType): void
    {
        self::$mockedResponseBody = $body;
        self::$mockedStatusCode = $statusCode;
        self::$mockedContentType = $contentType;
    }

    public static function getMockedResponseBody(): string
    {
        return self::$mockedResponseBody;
    }

    public static function getMockedStatusCode(): int
    {
        return self::$mockedStatusCode;
    }

    public static function getMockedContentType(): string
    {
        return self::$mockedContentType;
    }

    protected function setUp(): void
    {
        parent::setUp();

        Log::shouldReceive('warning')->andReturnNull();
        Log::shouldReceive('info')->andReturnNull();

        $httpClient = Mockery::mock(ClientInterface::class);
        $httpRequest = Mockery::mock(HttpRequest::class);

        $this->request = new NotificationRequest($httpClient, $httpRequest);
        $this->request->initialize([
            'api_key' => 'test_api_key',
            'api_secret' => 'test_api_secret',
            'merchant_id' => 'test_merchant',
            'testMode' => true,
        ]);
    }

    #[Test]
    public function it_handles_html_error_response_gracefully(): void
    {
        $htmlResponse = '<html><body><h1>500 Internal Server Error</h1></body></html>';
        self::setMockedCurlResponse($htmlResponse, 500, 'text/html');

        $response = $this->request->sendData(['hostedTokenizationId' => 'test-token-123']);

        $this->assertInstanceOf(NotificationResponse::class, $response);
        $this->assertEquals(500, $response->getHttpStatusCode());

        $responseData = $response->getData();
        $this->assertArrayHasKey('errors', $responseData);
        $this->assertEquals('GATEWAY_ERROR', $responseData['errors'][0]['code']);
        $this->assertEquals('Invalid response from payment gateway', $responseData['errors'][0]['message']);
    }

    #[Test]
    public function it_handles_xml_error_response_gracefully(): void
    {
        $xmlResponse = '<?xml version="1.0"?><error><message>Service unavailable</message></error>';
        self::setMockedCurlResponse($xmlResponse, 503, 'application/xml');

        $response = $this->request->sendData(['hostedTokenizationId' => 'test-token-123']);

        $this->assertInstanceOf(NotificationResponse::class, $response);
        $this->assertEquals(503, $response->getHttpStatusCode());

        $responseData = $response->getData();
        $this->assertArrayHasKey('errors', $responseData);
        $this->assertEquals('GATEWAY_ERROR', $responseData['errors'][0]['code']);
    }

    #[Test]
    public function it_handles_plain_text_error_response_gracefully(): void
    {
        $textResponse = 'Error: Gateway timeout';
        self::setMockedCurlResponse($textResponse, 504, 'text/plain');

        $response = $this->request->sendData(['hostedTokenizationId' => 'test-token-123']);

        $this->assertInstanceOf(NotificationResponse::class, $response);
        $this->assertEquals(504, $response->getHttpStatusCode());

        $responseData = $response->getData();
        $this->assertArrayHasKey('errors', $responseData);
        $this->assertEquals('GATEWAY_ERROR', $responseData['errors'][0]['code']);
    }

    #[Test]
    public function it_handles_invalid_json_response_gracefully(): void
    {
        $invalidJsonResponse = '{"error": "Invalid JSON"';
        self::setMockedCurlResponse($invalidJsonResponse, 400, 'application/json');

        $response = $this->request->sendData(['hostedTokenizationId' => 'test-token-123']);

        $this->assertInstanceOf(NotificationResponse::class, $response);
        $this->assertEquals(400, $response->getHttpStatusCode());

        $responseData = $response->getData();
        $this->assertArrayHasKey('errors', $responseData);
        $this->assertEquals('JSON_PARSE_ERROR', $responseData['errors'][0]['code']);
        $this->assertEquals('Failed to parse gateway response', $responseData['errors'][0]['message']);
    }

    #[Test]
    public function it_handles_valid_json_response_correctly(): void
    {
        $validJsonResponse = json_encode([
            'token' => ['id' => 'test-token-123'],
            'tokenStatus' => 'CREATED'
        ]);
        self::setMockedCurlResponse($validJsonResponse, 200, 'application/json');

        $response = $this->request->sendData(['hostedTokenizationId' => 'test-token-123']);

        $this->assertInstanceOf(NotificationResponse::class, $response);
        $this->assertEquals(200, $response->getHttpStatusCode());
        $this->assertTrue($response->isSuccessful());

        $responseData = $response->getData();
        $this->assertArrayHasKey('token', $responseData);
        $this->assertEquals('test-token-123', $responseData['token']['id']);
        $this->assertEquals('CREATED', $responseData['tokenStatus']);
    }

    #[Test]
    public function it_handles_text_json_content_type(): void
    {
        $validJsonResponse = json_encode([
            'token' => ['id' => 'test-token-456'],
            'tokenStatus' => 'UNCHANGED'
        ]);
        self::setMockedCurlResponse($validJsonResponse, 200, 'text/json');

        $response = $this->request->sendData(['hostedTokenizationId' => 'test-token-456']);

        $this->assertInstanceOf(NotificationResponse::class, $response);
        $this->assertEquals(200, $response->getHttpStatusCode());
        $this->assertTrue($response->isSuccessful());
    }

    #[Test]
    public function it_handles_json_with_charset_in_content_type(): void
    {
        $validJsonResponse = json_encode([
            'token' => ['id' => 'test-token-789'],
            'tokenStatus' => 'CREATED'
        ]);
        self::setMockedCurlResponse($validJsonResponse, 200, 'application/json; charset=utf-8');

        $response = $this->request->sendData(['hostedTokenizationId' => 'test-token-789']);

        $this->assertInstanceOf(NotificationResponse::class, $response);
        $this->assertEquals(200, $response->getHttpStatusCode());
        $this->assertTrue($response->isSuccessful());
    }

    #[Test]
    public function it_handles_worldline_error_response(): void
    {
        $errorResponse = json_encode([
            'errors' => [
                [
                    'id' => 'AUTHENTICATION_FAILURE',
                    'errorCode' => '10524',
                    'message' => 'No hosted tokenization session was found',
                ]
            ]
        ]);
        self::setMockedCurlResponse($errorResponse, 400, 'application/json');

        $response = $this->request->sendData(['hostedTokenizationId' => 'invalid-token']);

        $this->assertInstanceOf(NotificationResponse::class, $response);
        $this->assertEquals(400, $response->getHttpStatusCode());
        $this->assertFalse($response->isSuccessful());
    }

    #[Test]
    public function it_truncates_long_response_body_in_error_structure(): void
    {
        $longHtmlResponse = str_repeat('<div>Long HTML content</div>', 100);
        self::setMockedCurlResponse($longHtmlResponse, 500, 'text/html');

        $response = $this->request->sendData(['hostedTokenizationId' => 'test-token']);

        $this->assertInstanceOf(NotificationResponse::class, $response);
        $responseData = $response->getData();
        $this->assertEquals('GATEWAY_ERROR', $responseData['errors'][0]['code']);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}

function curl_init()
{
    return 'mock_handle';
}

function curl_setopt($ch, $option, $value)
{
    return true;
}

function curl_exec($ch)
{
    return NotificationRequestTest::getMockedResponseBody();
}

function curl_getinfo($ch, $option = null)
{
    if ($option === \CURLINFO_RESPONSE_CODE) {
        return NotificationRequestTest::getMockedStatusCode();
    }
    if ($option === \CURLINFO_CONTENT_TYPE) {
        return NotificationRequestTest::getMockedContentType();
    }
    return null;
}

function curl_close($ch)
{
    return true;
}
