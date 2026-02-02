<?php

namespace Ampeco\OmnipayWlValina;

use Ampeco\OmnipayWlValina\Message\AuthorizeRequest;
use Ampeco\OmnipayWlValina\Message\CaptureRequest;
use Ampeco\OmnipayWlValina\Message\CreateCardRequest;
use Ampeco\OmnipayWlValina\Message\CreateHostedCheckoutRequest;
use Ampeco\OmnipayWlValina\Message\DeleteCardRequest;
use Ampeco\OmnipayWlValina\Message\GetHostedCheckoutRequest;
use Ampeco\OmnipayWlValina\Message\GetPaymentDetailsRequest;
use Ampeco\OmnipayWlValina\Message\GetPaymentRequest;
use Ampeco\OmnipayWlValina\Message\InitialPurchaseRequest;
use Ampeco\OmnipayWlValina\Message\NotificationRequest;
use Ampeco\OmnipayWlValina\Message\PurchaseRequest;
use Ampeco\OmnipayWlValina\Message\RefundRequest;
use Ampeco\OmnipayWlValina\Message\VoidRequest;
use Ampeco\OmnipayWlValina\Message\RefundNotification;
use Omnipay\Common\AbstractGateway;

/**
 * @method \Omnipay\Common\Message\RequestInterface completeAuthorize(array $options = array())
 * @method \Omnipay\Common\Message\RequestInterface completePurchase(array $options = array())
 * @method \Omnipay\Common\Message\RequestInterface fetchTransaction(array $options = [])
 * @method \Omnipay\Common\Message\RequestInterface updateCard(array $options = array())
 */
class Gateway extends AbstractGateway
{
    use CommonParameters;

    public const WEBHOOK_NOTIFICATION_REFUND_TYPES = [
        'payment.refunded',
        'refund.rejected',
        'refund.refund_requested',
    ];

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

    public function createHostedCheckout(array $options = [])
    {
        return $this->createRequest(CreateHostedCheckoutRequest::class, $options);
    }

    public function getHostedCheckout(array $parameters = [])
    {
        return $this->createRequest(GetHostedCheckoutRequest::class, $parameters);
    }

    public function isHostedCheckoutReference(string $reference): bool
    {
        return (bool) preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $reference);
    }

    public function acceptNotification(array $requestData)
    {
        if (isset($requestData['type']) && in_array($requestData['type'], self::WEBHOOK_NOTIFICATION_REFUND_TYPES)) {
            return new RefundNotification($requestData);
        }

        if (isset($requestData['hostedTokenizationId'])) {
            return $this->createRequest(NotificationRequest::class, $requestData);
        }
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

    public function void(array $parameters)
    {
        return $this->createRequest(VoidRequest::class, $parameters);
    }

    public function initialPurchase(array $parameters)
    {
        return $this->createRequest(InitialPurchaseRequest::class, $parameters);
    }

    public function getPayment(array $parameters)
    {
        return $this->createRequest(GetPaymentRequest::class, $parameters);
    }

    public function getPaymentDetails(array $parameters)
    {
        return $this->createRequest(GetPaymentDetailsRequest::class, $parameters);
    }

    public function isHostedTokenizationReference(string $reference): bool
    {
        // Assumed based on real data, more info about hosted tokenization bellow
        // https://docs.direct.worldline-solutions.com/en/integration/basic-integration-methods/hosted-tokenization-page#sendcreatepaymentrequest
        // Nobody says it will always be 32 characters long
        return strlen($reference) === 32;
    }

    public function refund(array $parameters)
    {
        return $this->createRequest(RefundRequest::class, $parameters);
    }
}
