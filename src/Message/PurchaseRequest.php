<?php

namespace Ampeco\OmnipayWlValina\Message;

class PurchaseRequest extends AuthorizeRequest
{
    protected function createResponse(array $data, int $statusCode): Response
    {
        return new PurchaseResponse($this, $data, $statusCode);
    }

    public function getData(): array
    {
        $data = parent::getData();

        $data['cardPaymentMethodSpecificInput']['authorizationMode'] = 'SALE';
        $data['cardPaymentMethodSpecificInput']['threeDSecure']['challengeIndicator'] = 'no-challenge-requested';

        return $data;
    }
}
