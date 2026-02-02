<?php

declare(strict_types=1);

namespace Ampeco\OmnipayWlValina\Tests\Message;

use Ampeco\OmnipayWlValina\Message\CreateHostedCheckoutResponse;
use Omnipay\Common\Message\RequestInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Mockery;
use Mockery\MockInterface;

class CreateHostedCheckoutResponseTest extends TestCase
{
    private function createResponse(array $data, int $statusCode = 201, bool $testMode = false): CreateHostedCheckoutResponse
    {
        /** @var RequestInterface&MockInterface $request */
        $request = Mockery::mock(RequestInterface::class);
        $request->shouldReceive('getTestMode')->andReturn($testMode);

        return new CreateHostedCheckoutResponse($request, $data, $statusCode);
    }

    #[Test]
    public function it_is_always_a_redirect(): void
    {
        $response = $this->createResponse($this->successfulResponseData());

        $this->assertTrue($response->isRedirect());
    }

    #[Test]
    public function it_is_successful_with_hosted_checkout_id(): void
    {
        $response = $this->createResponse($this->successfulResponseData());

        $this->assertTrue($response->isSuccessful());
    }

    #[Test]
    public function it_is_not_successful_without_hosted_checkout_id(): void
    {
        $response = $this->createResponse(['partialRedirectUrl' => 'some-url']);

        $this->assertFalse($response->isSuccessful());
    }

    #[Test]
    public function it_is_not_successful_with_error_status_code(): void
    {
        $response = $this->createResponse($this->successfulResponseData(), 400);

        $this->assertFalse($response->isSuccessful());
    }

    #[Test]
    public function it_builds_redirect_url_from_partial_url(): void
    {
        $response = $this->createResponse($this->successfulResponseData());

        $this->assertEquals(
            'https://payment.preprod.direct.worldline-solutions.com/hostedcheckout/PaymentMethods/Selection/9a713afd426045f8a4c5b2e9439110e0',
            $response->getRedirectUrl()
        );
    }

    #[Test]
    public function it_returns_full_redirect_url_when_present(): void
    {
        $response = $this->createResponse([
            'hostedCheckoutId' => '15c09dac-bf44-486c-b584-1c1c25e89f81',
            'redirectUrl' => 'https://payment.preprod.direct.worldline-solutions.com/hostedcheckout/PaymentMethods/Selection/abc123',
            'partialRedirectUrl' => 'preprod.direct.worldline-solutions.com/hostedcheckout/PaymentMethods/Selection/abc123',
            'RETURNMAC' => 'some-return-mac-value',
        ]);

        $this->assertEquals(
            'https://payment.preprod.direct.worldline-solutions.com/hostedcheckout/PaymentMethods/Selection/abc123',
            $response->getRedirectUrl()
        );
    }

    #[Test]
    public function it_returns_empty_redirect_url_when_partial_url_missing(): void
    {
        $response = $this->createResponse(['hostedCheckoutId' => 'test-id']);

        $this->assertEquals('', $response->getRedirectUrl());
    }

    #[Test]
    public function it_extracts_transaction_reference(): void
    {
        $response = $this->createResponse($this->successfulResponseData());

        $this->assertEquals('15c09dac-bf44-486c-b584-1c1c25e89f81', $response->getTransactionReference());
    }

    private function successfulResponseData(): array
    {
        return [
            'hostedCheckoutId' => '15c09dac-bf44-486c-b584-1c1c25e89f81',
            'partialRedirectUrl' => 'preprod.direct.worldline-solutions.com/hostedcheckout/PaymentMethods/Selection/9a713afd426045f8a4c5b2e9439110e0',
            'RETURNMAC' => 'some-return-mac-value',
        ];
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
