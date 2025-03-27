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
                'authorizationMode' => $this->getUseFinalAuthInsteadOfSale() ? 'PRE_AUTHORIZATION' : 'FINAL_AUTHORIZATION',
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
                    'amount' => $this->getAmountInteger(),
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
