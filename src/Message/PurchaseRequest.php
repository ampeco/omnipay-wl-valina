<?php

namespace Ampeco\OmnipayWlValina\Message;

class PurchaseRequest extends AuthorizeRequest
{
    public function getData(): array
    {
        $data = parent::getData();

        $data['cardPaymentMethodSpecificInput']['authorizationMode'] = 'SALE';

        return $data;
    }
}
