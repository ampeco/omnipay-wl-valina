<?php

namespace Ampeco\OmnipayWlValina\Message;

class GetPaymentResponse extends Response
{
    public function getStatusCategory()
    {
        return $this->data['statusOutput']['statusCategory'] ?? [];
    }

    // TODO: Move the bellow 2 methods in the parent class
    // implemented based on - https://docs.direct.worldline-solutions.com/en/integration/api-developer-guide/statuses
    public function isPending(): bool
    {
        return in_array($this->getStatusCategory(), [
            self::STATUS_CATEGORY_CREATED,
            self::STATUS_CATEGORY_PENDING_PAYMENT,
            self::STATUS_CATEGORY_PENDING_MERCHANT,
            self::STATUS_CATEGORY_PENDING_CONNECT_OR_3RD_PARTY,
        ]);
    }

    public function isSuccessful(): bool
    {
        return parent::isSuccessful() && in_array($this->getStatusCategory(), [
            self::STATUS_CATEGORY_REFUNDED,
            self::STATUS_CATEGORY_COMPLETED,
        ]);
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
