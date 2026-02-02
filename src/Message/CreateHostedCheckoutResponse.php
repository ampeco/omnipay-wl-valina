<?php

declare(strict_types=1);

namespace Ampeco\OmnipayWlValina\Message;

use Omnipay\Common\Message\RedirectResponseInterface;

class CreateHostedCheckoutResponse extends Response implements RedirectResponseInterface
{
    public function isRedirect(): bool
    {
        return true;
    }

    public function isSuccessful(): bool
    {
        return parent::isSuccessful()
            && array_key_exists('hostedCheckoutId', $this->data);
    }

    public function getRedirectUrl(): string
    {
        if (!empty($this->data['redirectUrl'])) {
            return $this->data['redirectUrl'];
        }

        $partialUrl = $this->data['partialRedirectUrl'] ?? '';
        if (empty($partialUrl)) {
            return '';
        }

        return 'https://payment.' . $partialUrl;
    }

    public function getTransactionReference(): ?string
    {
        return $this->data['hostedCheckoutId'] ?? null;
    }
}
