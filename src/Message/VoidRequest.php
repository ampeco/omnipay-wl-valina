<?php

namespace Ampeco\OmnipayWlValina\Message;

class VoidRequest extends AbstractRequest
{
    public function getEndpoint(): string
    {
        return '/payments/' . $this->getTransactionReference() . '/cancel';
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
            'isFinal' => true,
        ];
    }

    protected function createResponse(array $data, int $statusCode): Response
    {
        return $this->response = new VoidResponse($this, $data, $statusCode);
    }
}
