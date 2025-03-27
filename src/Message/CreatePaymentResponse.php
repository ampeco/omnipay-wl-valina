<?php

namespace Ampeco\OmnipayWlValina\Message;

class CreatePaymentResponse extends Response
{
    public function getStatusCategory(): string
    {
        return $this->data['payment']['statusOutput']['statusCategory'] ?? '';
    }

    public function getStatus(): string
    {
        return $this->data['payment']['status'] ?? '';
    }

    public function isPending(): bool
    {
        return parent::isPending() || in_array($this->getStatusCategory(), [
            self::STATUS_CATEGORY_CREATED,
            self::STATUS_CATEGORY_PENDING_PAYMENT,
            self::STATUS_CATEGORY_PENDING_CONNECT_OR_3RD_PARTY,
        ]);
    }

    public function isRedirect(): bool
    {
        return $this->getStatus() == self::STATUS_REDIRECTED && $this->getRedirectUrl();
    }

    public function isSuccessful(): bool
    {
        if ($this->isRedirect()) {
            return false;
        }

        return parent::isSuccessful() && (
            $this->getStatusCategory() === self::STATUS_CATEGORY_REFUNDED ||
            $this->getStatusCategory() === self::STATUS_CATEGORY_COMPLETED ||
            ($this->getStatusCategory() === self::STATUS_CATEGORY_PENDING_MERCHANT && $this->getStatus() === self::STATUS_PENDING_CAPTURE)
        );
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
