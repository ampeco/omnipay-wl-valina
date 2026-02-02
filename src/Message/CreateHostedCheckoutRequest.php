<?php

declare(strict_types=1);

namespace Ampeco\OmnipayWlValina\Message;

class CreateHostedCheckoutRequest extends AbstractRequest
{
    public function getEndpoint(): string
    {
        return '/hostedcheckouts';
    }

    public function getRequestMethod(): string
    {
        return 'POST';
    }

    public function getData(): array
    {
        $data = [
            'hostedCheckoutSpecificInput' => array_filter([
                'returnUrl' => $this->getReturnUrl(),
                'locale' => $this->getLocale(),
                'variant' => $this->getTemplate(),
            ]),
            'order' => [
                'amountOfMoney' => [
                    'amount' => $this->getAmountInteger(),
                    'currencyCode' => $this->getCurrency(),
                ],
                'customer' => [
                    'contactDetails' => [
                        'emailAddress' => $this->getEmail(),
                    ],
                ],
            ],
            'cardPaymentMethodSpecificInput' => [
                'tokenize' => true,
                'threeDSecure' => [
                    'redirectionData' => [
                        'returnUrl' => $this->getThreeDSReturnUrl() ?? $this->getReturnUrl(),
                    ],
                ],
                'paymentProductFilters' => [
                    'restrictTo' => [
                        'groups' => ['cards'],
                    ],
                ],
            ],
        ];

        $challengeCanvasSize = $this->getThreeDSChallengeCanvasSize();
        if ($challengeCanvasSize) {
            $data['cardPaymentMethodSpecificInput']['threeDSecure']['challengeCanvasSize'] = $challengeCanvasSize;
        }

        return $data;
    }

    protected function createResponse(array $data, int $statusCode): Response
    {
        return new CreateHostedCheckoutResponse($this, $data, $statusCode);
    }
}
