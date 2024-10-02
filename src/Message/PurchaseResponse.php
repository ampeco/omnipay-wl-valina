<?php

namespace Ampeco\OmnipayWlValina\Message;

class PurchaseResponse extends Response
{
    public function isSuccessful(): bool
    {
        return parent::isSuccessful() && @$this->data['payment']['status'] === self::STATUS_CAPTURED;
    }

    public function getTransactionReference(): ?string
    {
        return $this->data['paymentResult']['payment']['id'] ?? parent::getTransactionReference();
    }
}
