<?php

namespace Ampeco\OmnipayWlValina\Unit;

use Ampeco\OmnipayWlValina\Gateway;
use Ampeco\OmnipayWlValina\Message\AuthorizeRequest;
use Ampeco\OmnipayWlValina\Message\GetPaymentDetailsRequest;
use Omnipay\Omnipay;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

class GatewayTest extends TestCase
{
    #[Test]
    public function it_uses_final_authorization_mode_when_session_authorization()
    {
        $gateway = Omnipay::create('\\' . Gateway::class);

        $request = $gateway->authorize([
            'amount' => 10,
            'currency' => 'EUR',
            'token' => 'token',
            'locale' => 'en_GB',
            'returnUrl' => 'http://test.com/return',

        ]);

        $this->assertTrue($request instanceof AuthorizeRequest);
        $this->assertEquals('FINAL_AUTHORIZATION', $request->getData()['cardPaymentMethodSpecificInput']['authorizationMode']);
    }

    #[Test]
    public function it_continues_to_use_final_authorization_mode_when_initial_purchase()
    {
        $gateway = Omnipay::create('\\' . Gateway::class);

        $request = $gateway->initialPurchase([
            'amount' => 10,
            'currency' => 'EUR',
            'token' => 'token',
            'locale' => 'en_GB',
            'returnUrl' => 'http://test.com/return',
            'hostedTokenizationId' => 'qwerty1234',
        ]);

        $this->assertTrue($request instanceof AuthorizeRequest);
        $this->assertEquals('FINAL_AUTHORIZATION', $request->getData()['cardPaymentMethodSpecificInput']['authorizationMode']);
    }

    #[Test]
    public function it_creates_get_payment_details_request()
    {
        $gateway = Omnipay::create('\\' . Gateway::class);

        $request = $gateway->getPaymentDetails([
            'payment_id' => 'test-payment-123',
        ]);

        $this->assertInstanceOf(GetPaymentDetailsRequest::class, $request);
    }
}
