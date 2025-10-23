<?php

namespace Ampeco\OmnipayWlValina\Message;

class GetPaymentResponse extends Response
{
    public function getStatus(): string
    {
        return $this->data['status'] ?? '';
    }

    public function getStatusCategory(): string
    {
        return $this->data['statusOutput']['statusCategory'] ?? '';
    }

    public function getStatusCode(): string
    {
        return $this->data['statusOutput']['statusCode'] ?? '';
    }

    public function isPending(): bool
    {
        return in_array($this->getStatusCategory(), [
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
            ($this->getStatusCategory() == self::STATUS_CATEGORY_PENDING_MERCHANT && $this->getStatus() === self::STATUS_PENDING_CAPTURE)
        );
    }

    public function getTransactionReference(): ?string
    {
        return $this->data['id'] ?? null;
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
