<?php

namespace Ampeco\OmnipayWlValina\Message;

class AuthorizeRequest extends AbstractRequest
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
                'token' => $this->getToken(),
                'unscheduledCardOnFileRequestor' => 'merchantInitiated',
                'unscheduledCardOnFileSequenceIndicator' => 'subsequent',
                'threeDSecure' => [
                    'skipAuthentication' => true,
                    'challengeIndicator' => 'no-challenge-requested',
                ],
            ],
            'order' => [
                'amountOfMoney' => [
                    //EUR is a 2-decimals currency, the value 1234 will result in EUR 12.34
                    'amount' => number_format($this->getAmount(), 2, '', ''),
                    'currencyCode' => $this->getCurrency(),
                ],
                ...$this->getCustomerData(),
            ],
        ];
    }

    public function getCustomerData(): array
    {
        return [];
    }

    protected function createResponse(array $data, int $statusCode): Response
    {
        return $this->response = new CreatePaymentResponse($this, $data, $statusCode);
    }
}
