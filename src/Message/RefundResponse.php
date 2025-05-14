<?php

namespace Ampeco\OmnipayWlValina\Message;

class RefundResponse extends Response
{

    public function isSuccessful(): bool
    {
        return parent::isSuccessful()
            && isset($this->data['id'])
            && isset($this->data['status'])
            && $this->data['status'] === self::STATUS_REFUND_REQUESTED;
    }
}
