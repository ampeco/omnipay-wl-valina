<?php

namespace Ampeco\OmnipayWlValina\Message;

class CaptureResponse extends Response
{
    public function isSuccessful(): bool
    {
        return parent::isSuccessful() && @$this->data['status'] === self::STATUS_CAPTURE_REQUESTED;
    }

    public function getTransactionReference(): ?string
    {
        return $this->data['id'] ?? parent::getTransactionReference();
    }
}
