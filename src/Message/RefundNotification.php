<?php

namespace Ampeco\OmnipayWlValina\Message;

use Omnipay\Common\Message\NotificationInterface;

class RefundNotification implements NotificationInterface
{
    public const REFUNDED = 'REFUNDED';
    public const REJECTED = 'REJECTED';
    public const REFUND_REQUESTED = 'REFUND_REQUESTED';

    public function __construct(protected array $data)
    {
    }

    public function getTransactionReference(): ?string
    {
        return $this->data['refund']['id'] ?? null;
    }

    public function getTransactionStatus(): ?string
    {
        if (!isset($this->data['refund']['status'])) {
            return null;
        }

        return match($this->data['refund']['status']) {
            self::REFUNDED => self::STATUS_COMPLETED,
            self::REJECTED => self::STATUS_FAILED,
            self::REFUND_REQUESTED => self::STATUS_PENDING,
            default => null,
        };
    }

    public function getMessage(): string
    {
        return '';
    }

    public function getData(): array
    {
        return $this->data;
    }
}
