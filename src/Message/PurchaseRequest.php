<?php

namespace Ampeco\OmnipayWlValina\Message;

class PurchaseRequest extends AuthorizeRequest
{
    public function getData(): array
    {
        $data = parent::getData();

        $data['cardPaymentMethodSpecificInput']['authorizationMode'] = 'FINAL_AUTHORIZATION';

        return $data;
    }
}
