<?php

namespace Ampeco\OmnipayWlValina\Tests\Message;

use Ampeco\OmnipayWlValina\Message\AuthorizeRequest;
use Omnipay\Common\Http\ClientInterface;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Mockery;

class AuthorizeRequestTest extends TestCase
{
    private AuthorizeRequest $request;

    public function setUp(): void
    {
        parent::setUp();
        
        // Create mock HTTP client and request with correct interfaces
        $httpClient = Mockery::mock(ClientInterface::class);
        $httpRequest = Mockery::mock(HttpRequest::class);
        
        $this->request = new AuthorizeRequest($httpClient, $httpRequest);
        $this->request->initialize([
            'amount' => '10.00',
            'currency' => 'EUR',
            'returnUrl' => 'https://example.com/return',
            'token' => 'test-token-123',
        ]);
    }

    #[Test]
    public function it_gets_data_without_descriptor(): void
    {
        $data = $this->request->getData();

        $this->assertArrayHasKey('order', $data);
        $this->assertArrayHasKey('amountOfMoney', $data['order']);
        $this->assertArrayNotHasKey('references', $data['order']);
    }

    #[Test]
    public function it_gets_data_with_descriptor(): void
    {
        $this->request->setDescriptor('Custom fee for parking overstay');
        $data = $this->request->getData();

        $this->assertArrayHasKey('order', $data);
        $this->assertArrayHasKey('references', $data['order']);
        $this->assertArrayHasKey('descriptor', $data['order']['references']);
        $this->assertEquals('Custom fee for parking overstay', $data['order']['references']['descriptor']);
    }

    #[Test]
    public function it_gets_data_with_empty_descriptor(): void
    {
        $this->request->setDescriptor('');
        $data = $this->request->getData();

        $this->assertArrayHasKey('order', $data);
        $this->assertArrayNotHasKey('references', $data['order']);
    }

    #[Test]
    public function it_gets_data_with_null_descriptor(): void
    {
        $this->request->setDescriptor(null);
        $data = $this->request->getData();

        $this->assertArrayHasKey('order', $data);
        $this->assertArrayNotHasKey('references', $data['order']);
    }

    #[Test]
    public function it_tests_descriptor_getter_setter(): void
    {
        $this->assertNull($this->request->getDescriptor());

        $this->request->setDescriptor('Test description');
        $this->assertEquals('Test description', $this->request->getDescriptor());

        $this->request->setDescriptor(null);
        $this->assertNull($this->request->getDescriptor());
    }

    #[Test]
    public function it_gets_data_with_enhanced_3ds_parameters(): void
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

    #[Test]
    public function it_gets_data_with_only_three_ds_return_url(): void
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

    #[Test]
    public function it_gets_data_with_only_challenge_canvas_size(): void
    {
        $this->request->setThreeDSChallengeCanvasSize('full-screen');
        
        $data = $this->request->getData();
        
        $this->assertArrayHasKey('cardPaymentMethodSpecificInput', $data);
        $this->assertArrayHasKey('threeDSecure', $data['cardPaymentMethodSpecificInput']);
        $this->assertArrayHasKey('challengeCanvasSize', $data['cardPaymentMethodSpecificInput']['threeDSecure']);
        $this->assertEquals('full-screen', $data['cardPaymentMethodSpecificInput']['threeDSecure']['challengeCanvasSize']);
        $this->assertArrayNotHasKey('redirectionData', $data['cardPaymentMethodSpecificInput']['threeDSecure']);
    }

    #[Test]
    public function it_gets_data_without_enhanced_3ds_parameters(): void
    {
        $data = $this->request->getData();
        
        $this->assertArrayHasKey('cardPaymentMethodSpecificInput', $data);
        $this->assertArrayHasKey('threeDSecure', $data['cardPaymentMethodSpecificInput']);
        $this->assertArrayNotHasKey('redirectionData', $data['cardPaymentMethodSpecificInput']['threeDSecure']);
        $this->assertArrayNotHasKey('challengeCanvasSize', $data['cardPaymentMethodSpecificInput']['threeDSecure']);
    }

    #[Test]
    public function it_tests_three_ds_parameter_getters_setters(): void
    {
        $this->assertNull($this->request->getThreeDSReturnUrl());
        $this->assertNull($this->request->getThreeDSChallengeCanvasSize());

        $this->request->setThreeDSReturnUrl('https://example.com/3ds-return');
        $this->assertEquals('https://example.com/3ds-return', $this->request->getThreeDSReturnUrl());

        $this->request->setThreeDSChallengeCanvasSize('full-screen');
        $this->assertEquals('full-screen', $this->request->getThreeDSChallengeCanvasSize());

        $this->request->setThreeDSReturnUrl(null);
        $this->assertNull($this->request->getThreeDSReturnUrl());

        $this->request->setThreeDSChallengeCanvasSize(null);
        $this->assertNull($this->request->getThreeDSChallengeCanvasSize());
    }
    
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
