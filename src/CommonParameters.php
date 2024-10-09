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

    public function setEmail($value): void
    {
        $this->setParameter('email', $value);
    }

    public function getEmail()
    {
        return $this->getParameter('email');
    }

    public function setCity($value): void
    {
        $this->setParameter('city', $value);
    }

    public function getCity()
    {
        return $this->getParameter('city');
    }

    public function setCountryCode($value): void
    {
        $this->setParameter('countryCode', $value);
    }

    public function getCountryCode()
    {
        return $this->getParameter('countryCode');
    }

    public function setStreet($value): void
    {
        $this->setParameter('street', $value);
    }

    public function getStreet()
    {
        return $this->getParameter('street');
    }

    public function setAcceptHeader($value): void
    {
        $this->setParameter('acceptHeader', $value);
    }

    public function getAcceptHeader()
    {
        return $this->getParameter('acceptHeader');
    }

    public function setUserAgent($value): void
    {
        $this->setParameter('userAgent', $value);
    }

    public function getUserAgent()
    {
        return $this->getParameter('userAgent');
    }
}
