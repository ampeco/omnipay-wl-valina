<?php

namespace Ampeco\OmnipayWlValina\Message;

class PurchaseRequest extends AuthorizeRequest
{
    public function getData(): array
    {
        $data = parent::getData();

        /**
         * TODO: Should be changed to
         * $data['cardPaymentMethodSpecificInput']['authorizationMode'] = 'FINAL_AUTHORIZATION';
         * Then almost immediate (after some delay) capture will send if the authorization is successful
         */
        $data['cardPaymentMethodSpecificInput']['authorizationMode'] = 'SALE';

        return $data;
    }
}
