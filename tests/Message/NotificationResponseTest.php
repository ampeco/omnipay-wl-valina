<?php

namespace Ampeco\OmnipayWlValina\Tests\Message;

use Ampeco\OmnipayWlValina\Message\NotificationResponse;
use Omnipay\Common\Message\NotificationInterface;
use Omnipay\Common\Message\RequestInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Mockery;
use Mockery\MockInterface;

class NotificationResponseTest extends TestCase
{
    #[Test]
    public function it_is_successful_with_missing_token_status(): void
    {
        /** @var RequestInterface&MockInterface $request */
        $request = Mockery::mock(RequestInterface::class);

        // Simulate error response from Worldline without token Status field
        $responseData = [
            'errors' => [
                [
                    'id' => 'AUTHENTICATION_FAILURE',
                    'errorCode' => '10524',
                    'message' => 'No hosted tokenization session was found',
                ]
            ]
        ];

        $response = new NotificationResponse($request, $responseData, 400);

        // This should not throw an "Undefined array key" error
        $this->assertFalse($response->isSuccessful());
    }

    #[Test]
    public function it_is_for_tokenization_with_missing_token_status(): void
    {
        /** @var RequestInterface&MockInterface $request */
        $request = Mockery::mock(RequestInterface::class);

        // Simulate error response from Worldline without token Status field
        $responseData = [
            'errors' => [
                [
                    'id' => 'AUTHENTICATION_FAILURE',
                    'errorCode' => '10524',
                    'message' => 'No hosted tokenization session was found',
                ]
            ]
        ];

        $response = new NotificationResponse($request, $responseData, 400);

        // This should not throw an "Undefined array key" error
        $this->assertFalse($response->isForTokenization());
    }

    #[Test]
    public function it_is_successful_with_valid_token_status_created(): void
    {
        /** @var RequestInterface&MockInterface $request */
        $request = Mockery::mock(RequestInterface::class);

        $responseData = [
            'token' => [
                'id' => 'test-token-123'
            ],
            'tokenStatus' => 'CREATED'
        ];

        $response = new NotificationResponse($request, $responseData, 200);

        $this->assertTrue($response->isSuccessful());
        $this->assertTrue($response->isForTokenization());
        $this->assertEquals('test-token-123', $response->getToken());
        $this->assertEquals(NotificationInterface::STATUS_COMPLETED, $response->getTransactionStatus());
    }

    #[Test]
    public function it_is_successful_with_valid_token_status_unchanged(): void
    {
        /** @var RequestInterface&MockInterface $request */
        $request = Mockery::mock(RequestInterface::class);

        $responseData = [
            'token' => [
                'id' => 'test-token-456'
            ],
            'tokenStatus' => 'UNCHANGED'
        ];

        $response = new NotificationResponse($request, $responseData, 200);

        $this->assertTrue($response->isSuccessful());
        $this->assertTrue($response->isForTokenization());
        $this->assertEquals('test-token-456', $response->getToken());
        $this->assertEquals(NotificationInterface::STATUS_COMPLETED, $response->getTransactionStatus());
    }

    #[Test]
    public function it_is_successful_with_invalid_token_status(): void
    {
        /** @var RequestInterface&MockInterface $request */
        $request = Mockery::mock(RequestInterface::class);

        $responseData = [
            'token' => [
                'id' => 'test-token-789'
            ],
            'tokenStatus' => 'INVALID_STATUS'
        ];

        $response = new NotificationResponse($request, $responseData, 200);

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isForTokenization());
        $this->assertEquals(NotificationInterface::STATUS_FAILED, $response->getTransactionStatus());
    }

    #[Test]
    public function it_is_successful_with_missing_token(): void
    {
        /** @var RequestInterface&MockInterface $request */
        $request = Mockery::mock(RequestInterface::class);

        $responseData = [
            'tokenStatus' => 'CREATED'
        ];

        $response = new NotificationResponse($request, $responseData, 200);

        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isForTokenization());
        $this->assertNull($response->getToken());
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
