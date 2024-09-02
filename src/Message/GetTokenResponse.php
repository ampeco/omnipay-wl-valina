<?php

namespace Ampeco\OmnipayWlValina\Message;

class GetTokenResponse extends Response
{
    public function isSuccessful(): bool
    {
        return parent::isSuccessful();
    }

    public function getToken()
    {
        return $this->data['id'];
    }

    public function getCardNumber()
    {
        return @$this->data['card']['alias'];
    }

    public function getExpiryDate()
    {
        return @$this->data['card']['data']['cardWithoutCvv']['expiryDate'];
    }
}
