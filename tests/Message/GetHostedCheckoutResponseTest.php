<?php

declare(strict_types=1);

namespace Ampeco\OmnipayWlValina\Tests\Message;

use Ampeco\OmnipayWlValina\Message\GetHostedCheckoutResponse;
use Omnipay\Common\Message\RequestInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Mockery;
use Mockery\MockInterface;

class GetHostedCheckoutResponseTest extends TestCase
{
    private function createResponse(array $data, int $statusCode = 200): GetHostedCheckoutResponse
    {
        /** @var RequestInterface&MockInterface $request */
        $request = Mockery::mock(RequestInterface::class);

        return new GetHostedCheckoutResponse($request, $data, $statusCode);
    }

    #[Test]
    public function it_is_successful_with_payment_created_status(): void
    {
        $response = $this->createResponse($this->successfulResponseData());

        $this->assertTrue($response->isSuccessful());
    }

    #[Test]
    public function it_is_not_successful_with_in_progress_status(): void
    {
        $data = $this->successfulResponseData();
        $data['status'] = 'IN_PROGRESS';

        $response = $this->createResponse($data);

        $this->assertFalse($response->isSuccessful());
    }

    #[Test]
    public function it_is_not_successful_with_error_status_code(): void
    {
        $response = $this->createResponse($this->successfulResponseData(), 400);

        $this->assertFalse($response->isSuccessful());
    }

    #[Test]
    public function it_is_not_successful_with_errors_in_response(): void
    {
        $data = [
            'status' => 'PAYMENT_CREATED',
            'errors' => [
                ['id' => 'ERROR', 'errorCode' => '123', 'message' => 'Something went wrong'],
            ],
        ];

        $response = $this->createResponse($data);

        $this->assertFalse($response->isSuccessful());
    }

    #[Test]
    public function it_extracts_token(): void
    {
        $response = $this->createResponse($this->successfulResponseData());

        $this->assertEquals('tokenized-card-token-123', $response->getToken());
    }

    #[Test]
    public function it_returns_null_token_when_missing(): void
    {
        $response = $this->createResponse(['status' => 'PAYMENT_CREATED']);

        $this->assertNull($response->getToken());
    }

    #[Test]
    public function it_extracts_card_number_with_bin(): void
    {
        $response = $this->createResponse($this->successfulResponseData());

        $cardNumber = $response->getCardNumber();

        $this->assertEquals('411111XXXXXXXXXX', $cardNumber);
    }

    #[Test]
    public function it_returns_masked_card_number_when_bin_missing(): void
    {
        $data = $this->successfulResponseData();
        unset($data['createdPaymentOutput']['payment']['paymentOutput']['cardPaymentMethodSpecificOutput']['card']['bin']);

        $response = $this->createResponse($data);

        $this->assertEquals('XXXXXXXXXXXXXXXX', $response->getCardNumber());
    }

    #[Test]
    public function it_extracts_expiry_date(): void
    {
        $response = $this->createResponse($this->successfulResponseData());

        $this->assertEquals('1225', $response->getExpiryDate());
    }

    #[Test]
    public function it_extracts_transaction_reference(): void
    {
        $response = $this->createResponse($this->successfulResponseData());

        $this->assertEquals('000000850010000188180000200001', $response->getTransactionReference());
    }

    #[Test]
    public function it_extracts_status(): void
    {
        $response = $this->createResponse($this->successfulResponseData());

        $this->assertEquals('PAYMENT_CREATED', $response->getStatus());
    }

    #[Test]
    public function it_returns_empty_status_when_missing(): void
    {
        $response = $this->createResponse([]);

        $this->assertEquals('', $response->getStatus());
    }

    #[Test]
    public function it_extracts_card_bin(): void
    {
        $response = $this->createResponse($this->successfulResponseData());

        $this->assertEquals('411111', $response->getCardBin());
    }

    private function successfulResponseData(): array
    {
        return [
            'status' => 'PAYMENT_CREATED',
            'createdPaymentOutput' => [
                'payment' => [
                    'id' => '000000850010000188180000200001',
                    'status' => 'CAPTURED',
                    'statusOutput' => [
                        'statusCategory' => 'COMPLETED',
                    ],
                    'paymentOutput' => [
                        'cardPaymentMethodSpecificOutput' => [
                            'token' => 'tokenized-card-token-123',
                            'card' => [
                                'cardNumber' => 'XXXXXXXXXXXXXXXX',
                                'bin' => '411111',
                                'expiryDate' => '1225',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
