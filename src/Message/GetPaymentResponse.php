<?php

namespace Ampeco\OmnipayWlValina\Message;

class GetPaymentResponse extends Response {

    public function isSuccessful(): bool
    {
        return parent::isSuccessful() && $this->data['status'] === self::STATUS_PENDING_CAPTURE;
    }
}
