<?php

declare(strict_types=1);

namespace Ampeco\OmnipayWlValina\Tests\Message;

use Ampeco\OmnipayWlValina\Message\CreateHostedCheckoutRequest;
use Omnipay\Common\Http\ClientInterface;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Mockery;

class CreateHostedCheckoutRequestTest extends TestCase
{
    private CreateHostedCheckoutRequest $request;

    public function setUp(): void
    {
        parent::setUp();

        $httpClient = Mockery::mock(ClientInterface::class);
        $httpRequest = Mockery::mock(HttpRequest::class);

        $this->request = new CreateHostedCheckoutRequest($httpClient, $httpRequest);
        $this->request->initialize([
            'amount' => '10.00',
            'currency' => 'EUR',
            'returnUrl' => 'https://example.com/return',
            'locale' => 'en_GB',
            'email' => 'user@example.com',
        ]);
    }

    #[Test]
    public function it_builds_correct_payload_structure(): void
    {
        $data = $this->request->getData();

        $this->assertArrayHasKey('hostedCheckoutSpecificInput', $data);
        $this->assertArrayHasKey('order', $data);
        $this->assertArrayHasKey('cardPaymentMethodSpecificInput', $data);
    }

    #[Test]
    public function it_includes_hosted_checkout_specific_input(): void
    {
        $data = $this->request->getData();

        $this->assertEquals('https://example.com/return', $data['hostedCheckoutSpecificInput']['returnUrl']);
        $this->assertEquals('en_GB', $data['hostedCheckoutSpecificInput']['locale']);
    }

    #[Test]
    public function it_includes_order_with_amount_and_customer(): void
    {
        $data = $this->request->getData();

        $this->assertEquals(1000, $data['order']['amountOfMoney']['amount']);
        $this->assertEquals('EUR', $data['order']['amountOfMoney']['currencyCode']);
        $this->assertEquals('user@example.com', $data['order']['customer']['contactDetails']['emailAddress']);
    }

    #[Test]
    public function it_includes_tokenize_true(): void
    {
        $data = $this->request->getData();

        $this->assertTrue($data['cardPaymentMethodSpecificInput']['tokenize']);
    }

    #[Test]
    public function it_includes_three_ds_return_url_from_main_return_url(): void
    {
        $data = $this->request->getData();

        $this->assertEquals(
            'https://example.com/return',
            $data['cardPaymentMethodSpecificInput']['threeDSecure']['redirectionData']['returnUrl']
        );
    }

    #[Test]
    public function it_uses_explicit_three_ds_return_url_when_set(): void
    {
        $this->request->setThreeDSReturnUrl('https://example.com/3ds-return');

        $data = $this->request->getData();

        $this->assertEquals(
            'https://example.com/3ds-return',
            $data['cardPaymentMethodSpecificInput']['threeDSecure']['redirectionData']['returnUrl']
        );
    }

    #[Test]
    public function it_includes_challenge_canvas_size_when_set(): void
    {
        $this->request->setThreeDSChallengeCanvasSize('full-screen');

        $data = $this->request->getData();

        $this->assertEquals(
            'full-screen',
            $data['cardPaymentMethodSpecificInput']['threeDSecure']['challengeCanvasSize']
        );
    }

    #[Test]
    public function it_excludes_challenge_canvas_size_when_not_set(): void
    {
        $data = $this->request->getData();

        $this->assertArrayNotHasKey(
            'challengeCanvasSize',
            $data['cardPaymentMethodSpecificInput']['threeDSecure']
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
