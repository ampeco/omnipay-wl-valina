<?php

namespace Ampeco\OmnipayWlValina\Tests\Message;

use Ampeco\OmnipayWlValina\Message\CreateCardRequest;
use Omnipay\Common\Http\ClientInterface;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use PHPUnit\Framework\TestCase;
use Mockery;

class CreateCardRequestTest extends TestCase
{
    private CreateCardRequest $request;

    public function setUp(): void
    {
        parent::setUp();
        
        $httpClient = Mockery::mock(ClientInterface::class);
        $httpRequest = Mockery::mock(HttpRequest::class);
        
        $this->request = new CreateCardRequest($httpClient, $httpRequest);
        $this->request->initialize([
            'template' => 'test-template',
            'locale' => 'en_US',
            'token' => 'test-token',
        ]);
    }

    public function testGetDataWithEnhanced3DSParameters(): void
    {
        $this->request->setThreeDSReturnUrl('https://example.com/3ds-return');
        $this->request->setThreeDSChallengeCanvasSize('full-screen');
        
        $data = $this->request->getData();
        
        $this->assertArrayHasKey('cardPaymentMethodSpecificInput', $data);
        $this->assertArrayHasKey('threeDSecure', $data['cardPaymentMethodSpecificInput']);
        $this->assertArrayHasKey('challengeCanvasSize', $data['cardPaymentMethodSpecificInput']['threeDSecure']);
        $this->assertEquals('full-screen', $data['cardPaymentMethodSpecificInput']['threeDSecure']['challengeCanvasSize']);
        $this->assertArrayHasKey('redirectionData', $data['cardPaymentMethodSpecificInput']['threeDSecure']);
        $this->assertArrayHasKey('returnUrl', $data['cardPaymentMethodSpecificInput']['threeDSecure']['redirectionData']);
        $this->assertEquals('https://example.com/3ds-return', $data['cardPaymentMethodSpecificInput']['threeDSecure']['redirectionData']['returnUrl']);
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
        
        $this->assertArrayHasKey('createCard', $data);
        $this->assertArrayHasKey('variant', $data);
        $this->assertArrayHasKey('locale', $data);
        $this->assertArrayHasKey('tokens', $data);
        $this->assertArrayNotHasKey('cardPaymentMethodSpecificInput', $data);
    }
    
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}