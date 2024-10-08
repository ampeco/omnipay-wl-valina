<?php

namespace Ampeco\OmnipayWlValina;

trait CommonParameters
{
    public function setMerchantId($value): void
    {
        $this->setParameter('merchantId', $value);
    }

    public function getMerchantId()
    {
        return $this->getParameter('merchantId');
    }

    public function setApiKey($value): void
    {
        $this->setParameter('api_key', $value);
    }

    public function getApiKey()
    {
        return $this->getParameter('api_key');
    }

    public function setApiSecret($value): void
    {
        $this->setParameter('api_secret', $value);
    }

    public function getApiSecret()
    {
        return $this->getParameter('api_secret');
    }

    public function setHostedTokenizationId($value): void
    {
        $this->setParameter('hostedTokenizationId', $value);
    }

    public function getHostedTokenizationId()
    {
        return $this->getParameter('hostedTokenizationId');
    }

    public function setPaymentId($value): void
    {
        $this->setParameter('paymentId', $value);
    }

    public function getPaymentId()
    {
        return $this->getParameter('paymentId');
    }

    public function setTemplate($value): void
    {
        $this->setParameter('template', $value);
    }

    public function getTemplate()
    {
        return $this->getParameter('template');
    }

    public function setLocale($value): void
    {
        $this->setParameter('locale', $value);
    }

    public function getLocale()
    {
        return $this->getParameter('locale');
    }

    public function setUserEmail($value): void
    {
        $this->setParameter('userEmail', $value);
    }

    public function getUserEmail()
    {
        return $this->getParameter('userEmail');
    }
}
