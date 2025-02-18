<?php

namespace Ampeco\OmnipayWlValina\Message;

class PurchaseRequest extends AuthorizeRequest
{
    public function getData(): array
    {
        $data = parent::getData();

        if ($this->getUseFinalAuthInsteadOfSale()) {
            $data['cardPaymentMethodSpecificInput']['authorizationMode'] = 'FINAL_AUTHORIZATION';
        } else {
            $data['cardPaymentMethodSpecificInput']['authorizationMode'] = 'SALE';
        }

        return $data;
    }
}
