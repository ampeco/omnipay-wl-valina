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
}
