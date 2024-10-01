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
                //EUR is a 2-decimals currency, the value 1234 will result in EUR 12.34
                'amount' => number_format($this->getAmount(), 2, '', ''),
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
