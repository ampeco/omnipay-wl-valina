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
        $maskedCardNumber = @$this->data['paymentOutput']['cardPaymentMethodSpecificOutput']['card']['cardNumber'];
        $cardBin = $this->getCardBin();

        return substr_replace($maskedCardNumber, $cardBin, 0, strlen($cardBin));
    }

    public function getCardBin()
    {
        return @$this->data['paymentOutput']['cardPaymentMethodSpecificOutput']['card']['bin'];
    }

    public function getExpiryDate()
    {
        return @$this->data['paymentOutput']['cardPaymentMethodSpecificOutput']['card']['expiryDate'];
    }
}
