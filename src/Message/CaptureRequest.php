<?php

namespace Ampeco\OmnipayWlValina\Message;

class CaptureRequest extends AbstractRequest
{

    public function getEndpoint(): string
    {
        return  '/payments/' . $this->getTransactionReference() . '/capture';
    }

    public function getData()
    {
        return [
            //EUR is a 2-decimals currency, the value 1234 will result in EUR 12.34
            'amount' => number_format($this->getAmount(), 2, '', ''),
        ];
    }

    public function getRequestMethod(): string
    {
        return 'POST';
    }

    protected function createResponse(array $data, int $statusCode): Response
    {
        return $this->response = new CaptureResponse($this, $data, $statusCode);
    }
}
