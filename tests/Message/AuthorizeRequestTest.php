<?php

namespace Ampeco\OmnipayWlValina\Tests\Message;

use Ampeco\OmnipayWlValina\Message\AuthorizeRequest;
use Omnipay\Common\Http\ClientInterface;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use PHPUnit\Framework\TestCase;
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

    public function testGetDataWithoutDescriptor(): void
    {
        $data = $this->request->getData();

        $this->assertArrayHasKey('order', $data);
        $this->assertArrayHasKey('amountOfMoney', $data['order']);
        $this->assertArrayNotHasKey('references', $data['order']);
    }

    public function testGetDataWithDescriptor(): void
    {
        $this->request->setDescriptor('Custom fee for parking overstay');
        $data = $this->request->getData();

        $this->assertArrayHasKey('order', $data);
        $this->assertArrayHasKey('references', $data['order']);
        $this->assertArrayHasKey('descriptor', $data['order']['references']);
        $this->assertEquals('Custom fee for parking overstay', $data['order']['references']['descriptor']);
    }

    public function testGetDataWithEmptyDescriptor(): void
    {
        $this->request->setDescriptor('');
        $data = $this->request->getData();

        $this->assertArrayHasKey('order', $data);
        $this->assertArrayNotHasKey('references', $data['order']);
    }

    public function testGetDataWithNullDescriptor(): void
    {
        $this->request->setDescriptor(null);
        $data = $this->request->getData();

        $this->assertArrayHasKey('order', $data);
        $this->assertArrayNotHasKey('references', $data['order']);
    }

    public function testDescriptorGetterSetter(): void
    {
        $this->assertNull($this->request->getDescriptor());

        $this->request->setDescriptor('Test description');
        $this->assertEquals('Test description', $this->request->getDescriptor());

        $this->request->setDescriptor(null);
        $this->assertNull($this->request->getDescriptor());
    }
    
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
