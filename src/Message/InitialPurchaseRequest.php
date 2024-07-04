<?php

namespace Ampeco\OmnipayWlValina\Message;

class InitialPurchaseRequest extends AbstractRequest
{
    public function getEndpoint(): string
    {
        return '/payments';
    }

    public function getRequestMethod(): string
    {
        return 'POST';
    }

    public function getData(): array
    {
        return [
            'cardPaymentMethodSpecificInput' => [
                'authorizationMode' => 'FINAL_AUTHORIZATION',
                'transactionChannel' => 'ECOMMERCE',
                'returnUrl' => $this->getReturnUrl(),
                'threeDSecure' => [
                    'skipAuthentication' => false,
                    'challengeIndicator' => 'no-preference',
                ],
            ],
            'order' => [
                'amountOfMoney' => [
                    //EUR is a 2-decimals currency, the value 1234 will result in EUR 12.34
                    'amount' => number_format($this->getAmount(), 2, '', ''),
                    'currencyCode' => $this->getCurrency(),
                ],
                'customer' => [
                    'device' => [
                        'acceptHeader' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
                        'userAgent' => 'Mozilla/5.0(WindowsNT10.0;Win64;x64)AppleWebKit/537.36(KHTML,likeGecko)Chrome/75.0.3770.142Safari/537.36',
                    ],
                ],
            ],
            'hostedTokenizationId' => $this->getHostedTokenizationId(),
        ];
    }

    protected function createResponse(array $data, int $statusCode): Response
    {
        return $this->response = new InitialPurchaseResponse($this, $data, $statusCode);
    }
}
