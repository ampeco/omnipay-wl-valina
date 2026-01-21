<?php

namespace Ampeco\OmnipayWlValina\Unit;

use Ampeco\OmnipayWlValina\Message\CreatePaymentResponse;
use Ampeco\OmnipayWlValina\Message\PurchaseRequest;
use Ampeco\OmnipayWlValina\Message\Response;
use Illuminate\Http\Request;
use Omnipay\Common\Http\Client;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;

class ResponseTest extends TestCase
{
    #[Test]
    #[DataProvider('responseClasses')]
    public function it_tests_response_is_pending_when_both_conditions_are_matched($responseClass)
    {
        $response = new $responseClass(request: (new PurchaseRequest(new Client(), new Request)), data: [
            'paymentResult' => [
                'payment' => [
                    'status' => 'PENDING_CAPTURE',
                    'statusOutput' => [
                        'errors' => [
                            [
                                'errorCode' => 20001111,
                            ],
                        ],
                        'isCancellable' => true,
                        'statusCategory' => 'PENDING_MERCHANT',
                        'statusCode' => 46,
                        'isAuthorized' => true,
                        'isRefundable' => false,
                    ],
                ],
            ],

        ], code: 200);

        $this->assertTrue($response->isPending());
    }

    #[Test]
    #[DataProvider('responseClasses')]
    public function it_tests_response_not_pending_when_error_code_matches_but_status_code_does_not($responseClass)
    {
        $response = new $responseClass(request: (new PurchaseRequest(new Client(), new Request)), data: [
            'paymentResult' => [
                'payment' => [
                    'status' => 'PENDING_CAPTURE',
                    'statusOutput' => [
                        'errors' => [
                            [
                                'errorCode' => 20001111,
                            ],
                        ],
                        'isCancellable' => true,
                        'statusCategory' => 'PENDING_MERCHANT',
                        'statusCode' => 2,
                        'isAuthorized' => true,
                        'isRefundable' => false,
                    ],
                ],
            ],

        ], code: 200);

        $this->assertNotTrue($response->isPending());
    }

    #[Test]
    #[DataProvider('responseClasses')]
    public function it_tests_response_not_pending_when_status_code_matches_but_error_code_does_not($responseClass)
    {
        $response = new $responseClass(request: (new PurchaseRequest(new Client(), new Request)), data: [
            'paymentResult' => [
                'payment' => [
                    'status' => 'PENDING_CAPTURE',
                    'statusOutput' => [
                        'errors' => [
                            [
                                'errorCode' => 11111111,
                            ],
                        ],
                        'isCancellable' => true,
                        'statusCategory' => 'PENDING_MERCHANT',
                        'statusCode' => 46,
                        'isAuthorized' => true,
                        'isRefundable' => false,
                    ],
                ],
            ],

        ], code: 200);

        $this->assertNotTrue($response->isPending());
    }

    #[Test]
    #[DataProvider('responseClasses')]
    public function it_returns_unsuccessful_when_errors_present_even_with_http_200($responseClass)
    {
        $response = new $responseClass(request: (new PurchaseRequest(new Client(), new Request)), data: [
            'errors' => [
                [
                    'id' => 'JSON_PARSE_ERROR',
                    'errorCode' => 502,
                    'message' => 'Failed to parse gateway response',
                ],
            ],
        ], code: 200);

        $this->assertFalse($response->isSuccessful());
    }

    #[Test]
    #[DataProvider('responseClasses')]
    public function it_returns_successful_when_no_errors_and_http_200($responseClass)
    {
        $response = new $responseClass(request: (new PurchaseRequest(new Client(), new Request)), data: [
            'payment' => [
                'id' => '12345',
                'status' => 'CREATED',
            ],
        ], code: 200);

        $this->assertTrue($response->isSuccessful());
    }

    #[Test]
    #[DataProvider('responseClasses')]
    public function it_returns_unsuccessful_when_http_error_code($responseClass)
    {
        $response = new $responseClass(request: (new PurchaseRequest(new Client(), new Request)), data: [
            'payment' => [
                'id' => '12345',
                'status' => 'REJECTED',
            ],
        ], code: 500);

        $this->assertFalse($response->isSuccessful());
    }

    public static function responseClasses(): array
    {
        return [
            'response' => [Response::class],
            'payment_response' => [CreatePaymentResponse::class],
        ];
    }
}
