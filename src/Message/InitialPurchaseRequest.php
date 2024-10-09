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
                    'challengeIndicator' => 'challenge-requested',
                ],
                'unscheduledCardOnFileRequestor' => 'cardholderInitiated',
                'unscheduledCardOnFileSequenceIndicator' => 'first',
            ],
            'order' => [
                'amountOfMoney' => [
                    //EUR is a 2-decimals currency, the value 1234 will result in EUR 12.34
                    'amount' => number_format($this->getAmount(), 2, '', ''),
                    'currencyCode' => $this->getCurrency(),
                ],
                'customer' => [
                    'device' => [
                        // TODO: Ask David is it acceptHeader or acceptHeaders. As well hardcoded like this in my opinion are only increasing the chance for fraud - can we get them from the SDK or somehow pass them to the notify url from valina.blade.php
                        'acceptHeader' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
                        'userAgent' => 'Mozilla/5.0(WindowsNT10.0;Win64;x64)AppleWebKit/537.36(KHTML,likeGecko)Chrome/75.0.3770.142Safari/537.36',
                        'locale' => $this->getLocale(),
                    ],
                    'contactDetails' => [
                        'emailAddress' => $this->getUserEmail(),
                    ],
                ],
            ],
            'hostedTokenizationId' => $this->getHostedTokenizationId(),
        ];
    }

    protected function createResponse(array $data, int $statusCode): Response
    {
        return $this->response = new CreatePaymentResponse($this, $data, $statusCode);
    }
}
