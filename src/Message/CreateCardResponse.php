<?php

namespace Ampeco\OmnipayWlValina\Message;

use Omnipay\Common\Message\RedirectResponseInterface;

class CreateCardResponse extends Response implements RedirectResponseInterface
{
    protected string $testMode;

    public function isRedirect(): bool
    {
        return true;
    }

    public function getHostedTokenizationId()
    {
        return $this->data['hostedTokenizationId'];
    }

    /** Because valina wants to build the tokenization page
    * Due to iframe security restrictions the HTML/Javascript code must be rendered in a view
    * Then we need to send the view to the mobile's Webview
    **/
    public function getRedirectUrl(): string
    {
       return '';
    }

    public function isSuccessful(): bool
    {
        return parent::isSuccessful()
            && array_key_exists('hostedTokenizationId', $this->data);
    }

    public function getTransactionReference(): ?string
    {
     return $this->getHostedTokenizationId();
    }
}
