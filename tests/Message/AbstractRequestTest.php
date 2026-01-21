<?php

declare(strict_types=1);

namespace Ampeco\OmnipayWlValina\Tests\Message;

use Ampeco\OmnipayWlValina\Gateway;
use Ampeco\OmnipayWlValina\Message\AbstractRequest;
use Ampeco\OmnipayWlValina\Message\Response;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Illuminate\Support\Facades\Log;
use Omnipay\Common\Http\ClientInterface;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Mockery;
use Symfony\Component\HttpFoundation\Request as HttpRequest;

class AbstractRequestTest extends TestCase
{
    private AbstractRequest $request;
    private ClientInterface $httpClient;
    private HttpRequest $httpRequest;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Mock the Log facade for all test methods
        Log::shouldReceive('warning')->andReturnNull();
        Log::shouldReceive('info')->andReturnNull();
        
        $this->httpClient = Mockery::mock(ClientInterface::class);
        $this->httpRequest = Mockery::mock(HttpRequest::class);
        
        // Create a concrete implementation of AbstractRequest for testing
        $this->request = new class($this->httpClient, $this->httpRequest) extends AbstractRequest {
            public function getEndpoint(): string
            {
                return '/payments';
            }
            
            public function getRequestMethod(): string
            {
                return 'POST';
            }
            
            protected function createResponse(array $data, int $statusCode): Response
            {
                return new Response($this, $data, $statusCode);
            }
        };
        
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
        
        $guzzleResponse = new GuzzleResponse(
            500, 
            ['Content-Type' => 'text/html'], 
            $htmlResponse
        );
        
        $this->httpClient
            ->shouldReceive('request')
            ->once()
            ->andReturn($guzzleResponse);
        
        $response = $this->request->sendData(['test' => 'data']);
        
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(500, $response->getHttpStatusCode());
        
        $responseData = $response->getData();
        $this->assertArrayHasKey('errors', $responseData);
        $this->assertEquals('GATEWAY_ERROR', $responseData['errors'][0]['id']);
        $this->assertEquals(500, $responseData['errors'][0]['errorCode']);
        $this->assertEquals('Invalid response from payment gateway', $responseData['errors'][0]['message']);
        $this->assertFalse($response->isSuccessful());
    }

    #[Test]
    public function it_handles_xml_error_response_gracefully(): void
    {
        $xmlResponse = '<?xml version="1.0"?><error><message>Service unavailable</message></error>';
        
        $guzzleResponse = new GuzzleResponse(
            503, 
            ['Content-Type' => 'application/xml'], 
            $xmlResponse
        );
        
        $this->httpClient
            ->shouldReceive('request')
            ->once()
            ->andReturn($guzzleResponse);
        
        $response = $this->request->sendData(['test' => 'data']);
        
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(503, $response->getHttpStatusCode());
        
        $responseData = $response->getData();
        $this->assertArrayHasKey('errors', $responseData);
        $this->assertEquals('GATEWAY_ERROR', $responseData['errors'][0]['id']);
        $this->assertEquals(503, $responseData['errors'][0]['errorCode']);
        $this->assertEquals('Invalid response from payment gateway', $responseData['errors'][0]['message']);
        $this->assertFalse($response->isSuccessful());
    }

    #[Test]
    public function it_handles_plain_text_error_response_gracefully(): void
    {
        $textResponse = 'Error: Gateway timeout';
        
        $guzzleResponse = new GuzzleResponse(
            504, 
            ['Content-Type' => 'text/plain'], 
            $textResponse
        );
        
        $this->httpClient
            ->shouldReceive('request')
            ->once()
            ->andReturn($guzzleResponse);
        
        $response = $this->request->sendData(['test' => 'data']);
        
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(504, $response->getHttpStatusCode());
        
        $responseData = $response->getData();
        $this->assertArrayHasKey('errors', $responseData);
        $this->assertEquals('GATEWAY_ERROR', $responseData['errors'][0]['id']);
        $this->assertEquals(504, $responseData['errors'][0]['errorCode']);
        $this->assertFalse($response->isSuccessful());
    }

    #[Test]
    public function it_handles_invalid_json_response_gracefully(): void
    {
        $invalidJsonResponse = '{"error": "Invalid JSON"'; // Missing closing brace
        
        $guzzleResponse = new GuzzleResponse(
            400, 
            ['Content-Type' => 'application/json'], 
            $invalidJsonResponse
        );
        
        $this->httpClient
            ->shouldReceive('request')
            ->once()
            ->andReturn($guzzleResponse);
        
        $response = $this->request->sendData(['test' => 'data']);
        
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(400, $response->getHttpStatusCode());
        
        $responseData = $response->getData();
        $this->assertArrayHasKey('errors', $responseData);
        $this->assertEquals('JSON_PARSE_ERROR', $responseData['errors'][0]['id']);
        $this->assertEquals(400, $responseData['errors'][0]['errorCode']);
        $this->assertStringStartsWith('Failed to parse gateway response:', $responseData['errors'][0]['message']);
        $this->assertFalse($response->isSuccessful());
    }

    #[Test]
    public function it_handles_valid_json_response_correctly(): void
    {
        $validJsonResponse = '{"payment": {"id": "12345", "status": "CREATED"}}';
        
        $guzzleResponse = new GuzzleResponse(
            200, 
            ['Content-Type' => 'application/json'], 
            $validJsonResponse
        );
        
        $this->httpClient
            ->shouldReceive('request')
            ->once()
            ->andReturn($guzzleResponse);
        
        $response = $this->request->sendData(['test' => 'data']);
        
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getHttpStatusCode());
        
        $responseData = $response->getData();
        $this->assertArrayHasKey('payment', $responseData);
        $this->assertEquals('12345', $responseData['payment']['id']);
        $this->assertEquals('CREATED', $responseData['payment']['status']);
    }

    #[Test]
    public function it_handles_response_without_content_type_header(): void
    {
        $response = 'Some unexpected response';
        
        $guzzleResponse = new GuzzleResponse(
            500, 
            [], // No Content-Type header
            $response
        );
        
        $this->httpClient
            ->shouldReceive('request')
            ->once()
            ->andReturn($guzzleResponse);
        
        $result = $this->request->sendData(['test' => 'data']);
        
        $this->assertInstanceOf(Response::class, $result);
        $this->assertEquals(500, $result->getHttpStatusCode());
        
        $responseData = $result->getData();
        $this->assertArrayHasKey('errors', $responseData);
        $this->assertEquals('GATEWAY_ERROR', $responseData['errors'][0]['id']);
        $this->assertEquals(500, $responseData['errors'][0]['errorCode']);
        $this->assertFalse($result->isSuccessful());
    }

    #[Test]
    public function it_returns_unsuccessful_when_http_200_but_errors_present(): void
    {
        $responseWithErrors = '{"errors": [{"id": "SOME_ERROR", "errorCode": 123, "message": "Something went wrong"}]}';

        $guzzleResponse = new GuzzleResponse(
            200,
            ['Content-Type' => 'application/json'],
            $responseWithErrors
        );

        $this->httpClient
            ->shouldReceive('request')
            ->once()
            ->andReturn($guzzleResponse);

        $response = $this->request->sendData(['test' => 'data']);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getHttpStatusCode());
        $this->assertFalse($response->isSuccessful());
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}