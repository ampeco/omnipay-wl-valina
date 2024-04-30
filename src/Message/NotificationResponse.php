<?php

namespace Ampeco\OmnipayWlValina\Message;

use Omnipay\Common\Message\NotificationInterface;

class NotificationResponse extends Response
{
    public function isSuccessful(): bool
    {
        return parent::isSuccessful()
            && array_key_exists('token', $this->data)
            && array_key_exists('id', $this->data['token'])
            && $this->data['tokenStatus'] === 'CREATED';
    }

    public function getToken()
    {
        return @$this->data['token']['id'];
    }

    public function getCardNumber()
    {
        return @$this->data['token']['card']['data']['cardWithoutCvv']['cardNumber'];
    }

    public function getTransactionStatus(): string
    {
        return $this->isSuccessful() ? NotificationInterface::STATUS_COMPLETED : NotificationInterface::STATUS_FAILED;
    }

    public function isForTokenization(): bool
    {
        return $this->data['tokenStatus'] === 'CREATED';
    }

    public function getExpiryDate()
    {
        return @$this->data['token']['card']['data']['cardWithoutCvv']['expiryDate'];
    }
}
