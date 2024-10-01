<?php

namespace Ampeco\OmnipayWlValina\Message;

class VoidResponse extends Response {

    public function isSuccessful(): bool
    {
        return parent::isSuccessful() && @$this->data['payment']['status'] === self::STATUS_CANCELLED;
    }
}
