<?php

namespace Ampeco\OmnipayWlValina\Tests\Message;

use Ampeco\OmnipayWlValina\Message\NotificationResponse;
use Omnipay\Common\Message\NotificationInterface;
use Omnipay\Common\Message\RequestInterface;
use PHPUnit\Framework\TestCase;
use Mockery;
use Mockery\MockInterface;

class NotificationResponseTest extends TestCase
{
    public function testIsSuccessfulWithMissingTokenStatus(): void
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

    public function testIsForTokenizationWithMissingTokenStatus(): void
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

    public function testIsSuccessfulWithValidTokenStatusCreated(): void
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

    public function testIsSuccessfulWithValidTokenStatusUnchanged(): void
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

    public function testIsSuccessfulWithInvalidTokenStatus(): void
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

    public function testIsSuccessfulWithMissingToken(): void
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
