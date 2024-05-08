<?php

namespace Ampeco\OmnipayWlValina\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;

class Response extends AbstractResponse
{
    const STATUS_CAPTURED = 'CAPTURED';
    const STATUS_PENDING_CAPTURE = 'PENDING_CAPTURE';
    const STATUS_CAPTURE_REQUESTED = 'CAPTURE_REQUESTED';

    public function __construct(RequestInterface $request, $data, protected int $code)
    {
        parent::__construct($request, $data);
    }

    /**
     * @inheritDoc
     */
    public function isSuccessful(): bool
    {
        return $this->code >= 200;
    }

    public function getTransactionReference(): ?string
    {
        return @$this->data['payment']['id'] ?? null;
    }
}
