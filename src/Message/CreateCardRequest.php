<?php

namespace Ampeco\OmnipayWlValina\Message;

use Omnipay\Common\Http\ClientInterface;
use Symfony\Component\HttpFoundation\Request as HttpRequest;

/**
 * @method \Omnipay\Common\Message\NotificationInterface acceptNotification(array $options = array())
 * @method \Omnipay\Common\Message\RequestInterface authorize(array $options = array())
 * @method \Omnipay\Common\Message\RequestInterface completeAuthorize(array $options = array())
 * @method \Omnipay\Common\Message\RequestInterface capture(array $options = array())
 * @method \Omnipay\Common\Message\RequestInterface purchase(array $options = array())
 * @method \Omnipay\Common\Message\RequestInterface completePurchase(array $options = array())
 * @method \Omnipay\Common\Message\RequestInterface refund(array $options = array())
 * @method \Omnipay\Common\Message\RequestInterface fetchTransaction(array $options = [])
 * @method \Omnipay\Common\Message\RequestInterface void(array $options = array())
 * @method \Omnipay\Common\Message\RequestInterface createCard(array $options = array())
 * @method \Omnipay\Common\Message\RequestInterface updateCard(array $options = array())
 * @method \Omnipay\Common\Message\RequestInterface deleteCard(array $options = array())
 */
class CreateCardRequest extends AbstractRequest
{
    public function __construct(ClientInterface $httpClient, HttpRequest $httpRequest)
    {
        parent::__construct($httpClient, $httpRequest);
    }

    public function getEndpoint(): string
    {
        return '/hostedtokenizations';
    }

    public function getRequestMethod(): string
    {
        return 'POST';
    }

    /**
     * @inheritDoc
     */
    public function getData()
    {
        $data = [
            'createCard' => true,
            'variant' => $this->getTemplate(),
            'locale' => $this->getLocale(),
            'tokens' => $this->getToken(),
        ];

        $challengeCanvasSize = $this->getThreeDSChallengeCanvasSize();
        $threeDSReturnUrl = $this->getThreeDSReturnUrl();

        if ($challengeCanvasSize || $threeDSReturnUrl) {
            $data['cardPaymentMethodSpecificInput'] = [
                'threeDSecure' => [],
            ];

            if ($challengeCanvasSize) {
                $data['cardPaymentMethodSpecificInput']['threeDSecure']['challengeCanvasSize'] = $challengeCanvasSize;
            }

            if ($threeDSReturnUrl) {
                $data['cardPaymentMethodSpecificInput']['threeDSecure']['redirectionData']['returnUrl'] = $threeDSReturnUrl;
            }
        }

        return $data;
    }

    protected function createResponse(array $data, int $statusCode): Response
    {
        return new CreateCardResponse($this, $data, $statusCode);
    }
}
