<?php

namespace Ampeco\OmnipayWlValina;

use Ampeco\OmnipayWlValina\Message\AuthorizeRequest;
use Ampeco\OmnipayWlValina\Message\CaptureRequest;
use Ampeco\OmnipayWlValina\Message\CreateCardRequest;
use Ampeco\OmnipayWlValina\Message\DeleteCardRequest;
use Ampeco\OmnipayWlValina\Message\NotificationRequest;
use Ampeco\OmnipayWlValina\Message\PurchaseRequest;
use Omnipay\Common\AbstractGateway;

/**
 * @method \Omnipay\Common\Message\RequestInterface completeAuthorize(array $options = array())
 * @method \Omnipay\Common\Message\RequestInterface completePurchase(array $options = array())
 * @method \Omnipay\Common\Message\RequestInterface refund(array $options = array())
 * @method \Omnipay\Common\Message\RequestInterface fetchTransaction(array $options = [])
 * @method \Omnipay\Common\Message\RequestInterface void(array $options = array())
 * @method \Omnipay\Common\Message\RequestInterface updateCard(array $options = array())
 */
class Gateway extends AbstractGateway
{
    use CommonParameters;

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'Valina';
    }

    public function createCard(array $options = [])
    {
        return $this->createRequest(CreateCardRequest::class, $options);
    }

    public function acceptNotification(array $requestData)
    {
        return $this->createRequest(NotificationRequest::class, $requestData);
    }

    public function deleteCard(array $parameters = [])
    {
        return $this->createRequest(DeleteCardRequest::class, $parameters);
    }

    public function purchase(array $parameters)
    {
        return $this->createRequest(PurchaseRequest::class, $parameters);
    }

    public function authorize(array $parameters)
    {
        return $this->createRequest(AuthorizeRequest::class, $parameters);
    }

    public function capture(array $parameters)
    {
        return $this->createRequest(CaptureRequest::class, $parameters);
    }


    public function __call(string $name, array $arguments)
    {
        // TODO: Implement @method \Omnipay\Common\Message\NotificationInterface acceptNotification(array $options = array())
        // TODO: Implement @method \Omnipay\Common\Message\RequestInterface authorize(array $options = array())
        // TODO: Implement @method \Omnipay\Common\Message\RequestInterface completeAuthorize(array $options = array())
        // TODO: Implement @method \Omnipay\Common\Message\RequestInterface capture(array $options = array())
        // TODO: Implement @method \Omnipay\Common\Message\RequestInterface purchase(array $options = array())
        // TODO: Implement @method \Omnipay\Common\Message\RequestInterface completePurchase(array $options = array())
        // TODO: Implement @method \Omnipay\Common\Message\RequestInterface refund(array $options = array())
        // TODO: Implement @method \Omnipay\Common\Message\RequestInterface fetchTransaction(array $options = [])
        // TODO: Implement @method \Omnipay\Common\Message\RequestInterface void(array $options = array())
    }



    public function getMerchantId()
    {
        return $this->getParameter('merchant_id');
    }

    public function getApiKey()
    {
        return $this->getParameter('api_key');
    }

    public function getApiSecret()
    {
        return $this->getParameter('api_secret');
    }
}
