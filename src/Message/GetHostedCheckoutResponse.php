<?php

declare(strict_types=1);

namespace Ampeco\OmnipayWlValina\Message;

class GetHostedCheckoutResponse extends Response
{
    public function getStatus(): string
    {
        return $this->data['status'] ?? '';
    }

    public function getCreatedPaymentOutput(): array
    {
        return $this->data['createdPaymentOutput'] ?? [];
    }

    public function isInProgress(): bool
    {
        return $this->getStatus() === 'IN_PROGRESS';
    }

    public function isSuccessful(): bool
    {
        if ($this->hasErrors()) {
            return false;
        }

        $status = $this->getStatus();

        return $status === 'PAYMENT_CREATED' && $this->code >= 200 && $this->code < 300;
    }

    public function getTransactionReference(): ?string
    {
        return $this->getCreatedPaymentOutput()['payment']['id'] ?? null;
    }

    public function getToken()
    {
        return $this->getCreatedPaymentOutput()['payment']['paymentOutput']['cardPaymentMethodSpecificOutput']['token'] ?? null;
    }

    public function getCardNumber()
    {
        $cardData = $this->getCreatedPaymentOutput()['payment']['paymentOutput']['cardPaymentMethodSpecificOutput']['card'] ?? [];
        $maskedCardNumber = $cardData['cardNumber'] ?? '';
        $cardBin = $this->getCardBin();

        if (empty($maskedCardNumber) || empty($cardBin)) {
            return $maskedCardNumber;
        }

        return substr_replace($maskedCardNumber, $cardBin, 0, strlen($cardBin));
    }

    public function getCardBin()
    {
        return $this->getCreatedPaymentOutput()['payment']['paymentOutput']['cardPaymentMethodSpecificOutput']['card']['bin'] ?? null;
    }

    public function getExpiryDate()
    {
        return $this->getCreatedPaymentOutput()['payment']['paymentOutput']['cardPaymentMethodSpecificOutput']['card']['expiryDate'] ?? null;
    }
}
