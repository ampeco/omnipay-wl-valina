<?php

namespace Ampeco\OmnipayWlValina\Tests\Message;

use Ampeco\OmnipayWlValina\Message\GetPaymentDetailsRequest;
use Ampeco\OmnipayWlValina\Message\GetPaymentResponse;
use Omnipay\Common\Http\ClientInterface;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Mockery;

class GetPaymentDetailsRequestTest extends TestCase
{
    private GetPaymentDetailsRequest $request;

    public function setUp(): void
    {
        parent::setUp();

        $httpClient = Mockery::mock(ClientInterface::class);
        $httpRequest = Mockery::mock(HttpRequest::class);

        $this->request = new GetPaymentDetailsRequest($httpClient, $httpRequest);
        $this->request->initialize([
            'payment_id' => 'test-payment-id-123',
            'merchantId' => 'test-merchant',
        ]);
    }

    #[Test]
    public function it_generates_correct_endpoint(): void
    {
        $endpoint = $this->request->getEndpoint();

        $this->assertEquals('/payments/test-payment-id-123/details', $endpoint);
    }

    #[Test]
    public function it_uses_get_request_method(): void
    {
        $method = $this->request->getRequestMethod();

        $this->assertEquals('GET', $method);
    }

    #[Test]
    public function it_creates_get_payment_response(): void
    {
        $data = ['id' => 'test-payment-id-123', 'status' => 'CAPTURED'];
        $statusCode = 200;

        $response = $this->request->createResponse($data, $statusCode);

        $this->assertInstanceOf(GetPaymentResponse::class, $response);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
