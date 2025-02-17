<?php

namespace Ampeco\OmnipayWlValina\Unit;

use Ampeco\OmnipayWlValina\Gateway;
use Ampeco\OmnipayWlValina\Message\AuthorizeRequest;
use Omnipay\Omnipay;
use PHPUnit\Framework\TestCase;

class GatewayTest extends TestCase
{
    /**
     * @test
     */
    public function it_uses_pre_authorize_mode_when_session_authorization()
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
        $this->assertEquals('PRE_AUTHORIZATION', $request->getData()['cardPaymentMethodSpecificInput']['authorizationMode']);
    }

    /**
     * @test
     */
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
}
