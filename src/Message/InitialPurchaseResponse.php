<?php

namespace Ampeco\OmnipayWlValina\Message;

class InitialPurchaseResponse extends Response
{
    public function isSuccessful(): bool
    {
        return parent::isSuccessful() && in_array(@$this->data['payment']['status'], [self::STATUS_REDIRECTED, self::STATUS_PENDING_CAPTURE]);
    }

    public function getRedirectUrl()
    {
        return $this->data['merchantAction']['redirectData']['redirectURL'] ?? null;
    }

    public function getTransactionReference(): ?string
    {
        return $this->data['payment']['id'] ?? null;
    }
}
