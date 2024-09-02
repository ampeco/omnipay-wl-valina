<?php

namespace Ampeco\OmnipayWlValina\Message;

class GetPaymentResponse extends Response {

    public function isSuccessful(): bool
    {
        return parent::isSuccessful() && $this->data['status'] === self::STATUS_PENDING_CAPTURE;
    }

    public function getToken()
    {
        return $this->data['paymentOutput']['cardPaymentMethodSpecificOutput']['token'];
    }

    public function getCardNumber()
    {
        return @$this->data['paymentOutput']['cardPaymentMethodSpecificOutput']['card']['cardNumber'];
    }

    public function getExpiryDate()
    {
        return @$this->data['paymentOutput']['cardPaymentMethodSpecificOutput']['card']['expiryDate'];
    }

}
