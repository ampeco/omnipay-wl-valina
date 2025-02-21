<?php

namespace Ampeco\OmnipayWlValina\Unit;

use Ampeco\OmnipayWlValina\Message\CreatePaymentResponse;
use Ampeco\OmnipayWlValina\Message\PurchaseRequest;
use Ampeco\OmnipayWlValina\Message\Response;
use Illuminate\Http\Request;
use Omnipay\Common\Http\Client;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
    /**
     * @dataProvider responseClasses
     * @test
     */
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

    /**
     * @dataProvider responseClasses
     * @test
     */
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

    /**
     * @dataProvider responseClasses
     * @test
     */
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

    public static function responseClasses(): array
    {
        return [
            'response' => [Response::class],
            'payment_response' => [CreatePaymentResponse::class],
        ];
    }
}
