<?php

namespace Ampeco\OmnipayWlValina\Message;

class AuthorizeResponse extends Response
{
    public function isSuccessful(): bool
    {
        return parent::isSuccessful() && @$this->data['payment']['status'] === self::STATUS_PENDING_CAPTURE;
    }
}
