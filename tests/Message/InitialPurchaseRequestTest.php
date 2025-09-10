<?php

namespace Ampeco\OmnipayWlValina\Tests\Message;

use Ampeco\OmnipayWlValina\Message\InitialPurchaseRequest;
use Omnipay\Common\Http\ClientInterface;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use PHPUnit\Framework\TestCase;
use Mockery;

class InitialPurchaseRequestTest extends TestCase
{
    private InitialPurchaseRequest $request;

    public function setUp(): void
    {
        parent::setUp();
        
        $httpClient = Mockery::mock(ClientInterface::class);
        $httpRequest = Mockery::mock(HttpRequest::class);
        
        $this->request = new InitialPurchaseRequest($httpClient, $httpRequest);
        $this->request->initialize([
            'amount' => '10.00',
            'currency' => 'EUR',
            'returnUrl' => 'https://example.com/return',
            'hostedTokenizationId' => 'test-token-id',
        ]);
    }

    public function testGetDataWithEnhanced3DSParameters(): void
    {
        $this->request->setThreeDSReturnUrl('https://example.com/3ds-return');
        $this->request->setThreeDSChallengeCanvasSize('full-screen');
        
        $data = $this->request->getData();
        
        $this->assertArrayHasKey('cardPaymentMethodSpecificInput', $data);
        $this->assertArrayHasKey('threeDSecure', $data['cardPaymentMethodSpecificInput']);
        $this->assertArrayHasKey('redirectionData', $data['cardPaymentMethodSpecificInput']['threeDSecure']);
        $this->assertArrayHasKey('returnUrl', $data['cardPaymentMethodSpecificInput']['threeDSecure']['redirectionData']);
        $this->assertEquals('https://example.com/3ds-return', $data['cardPaymentMethodSpecificInput']['threeDSecure']['redirectionData']['returnUrl']);
        $this->assertArrayHasKey('challengeCanvasSize', $data['cardPaymentMethodSpecificInput']['threeDSecure']);
        $this->assertEquals('full-screen', $data['cardPaymentMethodSpecificInput']['threeDSecure']['challengeCanvasSize']);
    }

    public function testGetDataWithOnlyThreeDSReturnUrl(): void
    {
        $this->request->setThreeDSReturnUrl('https://example.com/3ds-return');
        
        $data = $this->request->getData();
        
        $this->assertArrayHasKey('cardPaymentMethodSpecificInput', $data);
        $this->assertArrayHasKey('threeDSecure', $data['cardPaymentMethodSpecificInput']);
        $this->assertArrayHasKey('redirectionData', $data['cardPaymentMethodSpecificInput']['threeDSecure']);
        $this->assertArrayHasKey('returnUrl', $data['cardPaymentMethodSpecificInput']['threeDSecure']['redirectionData']);
        $this->assertEquals('https://example.com/3ds-return', $data['cardPaymentMethodSpecificInput']['threeDSecure']['redirectionData']['returnUrl']);
        $this->assertArrayNotHasKey('challengeCanvasSize', $data['cardPaymentMethodSpecificInput']['threeDSecure']);
    }

    public function testGetDataWithOnlyChallengeCanvasSize(): void
    {
        $this->request->setThreeDSChallengeCanvasSize('full-screen');
        
        $data = $this->request->getData();
        
        $this->assertArrayHasKey('cardPaymentMethodSpecificInput', $data);
        $this->assertArrayHasKey('threeDSecure', $data['cardPaymentMethodSpecificInput']);
        $this->assertArrayHasKey('challengeCanvasSize', $data['cardPaymentMethodSpecificInput']['threeDSecure']);
        $this->assertEquals('full-screen', $data['cardPaymentMethodSpecificInput']['threeDSecure']['challengeCanvasSize']);
        $this->assertArrayNotHasKey('redirectionData', $data['cardPaymentMethodSpecificInput']['threeDSecure']);
    }

    public function testGetDataWithoutEnhanced3DSParameters(): void
    {
        $data = $this->request->getData();
        
        $this->assertArrayHasKey('cardPaymentMethodSpecificInput', $data);
        $this->assertArrayHasKey('threeDSecure', $data['cardPaymentMethodSpecificInput']);
        $this->assertArrayNotHasKey('redirectionData', $data['cardPaymentMethodSpecificInput']['threeDSecure']);
        $this->assertArrayNotHasKey('challengeCanvasSize', $data['cardPaymentMethodSpecificInput']['threeDSecure']);
    }
    
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}