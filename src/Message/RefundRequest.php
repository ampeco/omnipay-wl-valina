<?php

namespace Ampeco\OmnipayWlValina\Message;

class RefundRequest extends AbstractRequest
{
    public function getEndpoint(): string
    {
        return '/payments/' . $this->getTransactionReference() . '/refund';
    }

    public function getRequestMethod(): string
    {
        return 'POST';
    }

    public function getData()
    {
        return [
            'amountOfMoney' => [
                'amount' => $this->getAmountInteger(),
                'currencyCode' => $this->getCurrency(),
            ],
        ];
    }

    protected function createResponse(array $data, int $statusCode): Response
    {
        return $this->response = new RefundResponse($this, $data, $statusCode);
    }
}
