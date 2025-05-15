<?php

namespace Ampeco\OmnipayWlValina\Message;

class RefundResponse extends Response
{

    public function isSuccessful(): bool
    {
        return parent::isSuccessful()
            && isset($this->data['id'])
            && isset($this->data['status'])
            && $this->data['status'] === self::STATUS_REFUND_REQUESTED;
    }

    public function getTransactionReference(): string
    {
        return $this->data['id'] ?? '';
    }

    public function getMessage(): string
    {
        if (!isset($this->data['errorId'])
            || !isset($this->data['errors'])
            || empty($this->data['errors'])
        ) {
            return '';
        }

        return implode(', ',
            array_map(fn ($error) =>
                'Error ID: ' . $error['id']
                . ', Error Code: ' . ($error['errorCode'] ?? '')
                . ', Error Message: ' . ($error['message'] ?? ''),
                $this->data['errors'],
            )
        );
    }
}
