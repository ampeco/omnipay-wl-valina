<?php

namespace Ampeco\OmnipayWlValina\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;

class Response extends AbstractResponse
{
    const STATUS_CREATED = 'CREATED';
    const STATUS_CANCELLED = 'CANCELLED';
    const STATUS_REJECTED = 'REJECTED';
    const STATUS_REJECTED_CAPTURE = 'REJECTED_CAPTURE';
    const STATUS_REDIRECTED = 'REDIRECTED';
    const STATUS_PENDING_CAPTURE = 'PENDING_CAPTURE';
    const STATUS_AUTHORIZATION_REQUESTED = 'AUTHORIZATION_REQUESTED';
    const STATUS_CAPTURE_REQUESTED = 'CAPTURE_REQUESTED';
    const STATUS_CAPTURED = 'CAPTURED';
    const STATUS_REFUND_REQUESTED = 'REFUND_REQUESTED';
    const STATUS_REFUNDED = 'REFUNDED';

    public function __construct(RequestInterface $request, $data, protected int $code)
    {
        parent::__construct($request, $data);
    }

    /**
     * @inheritDoc
     */
    public function isSuccessful(): bool
    {
        return $this->code >= 200 && $this->code < 300;
    }

    public function getTransactionReference(): ?string
    {
        return @$this->data['payment']['id'] ?? null;
    }
}
