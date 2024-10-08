<?php

namespace Ampeco\OmnipayWlValina\Message;

/**
 * TODO: This must be combined with PurchaseResponse
 */
class InitialPurchaseResponse extends Response
{
    public function getStatusCategory()
    {
        return $this->data['payment']['statusOutput']['statusCategory'] ?? [];
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

    public function getRedirectUrl()
    {
        return $this->data['merchantAction']['redirectData']['redirectURL'] ?? null;
    }

    public function getTransactionReference(): ?string
    {
        return $this->data['paymentResult']['payment']['id'] ?? parent::getTransactionReference();
    }

    public function getCode()
    {
        return @$this->data['errors'][0]['errorCode'] ?? parent::getCode();
    }
}
