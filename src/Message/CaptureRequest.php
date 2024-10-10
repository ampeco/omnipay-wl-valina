<?php

namespace Ampeco\OmnipayWlValina\Message;

class CaptureRequest extends AbstractRequest
{
    public function getEndpoint(): string
    {
        return '/payments/' . $this->getTransactionReference() . '/capture';
    }

    public function getData()
    {
        return [
            'amount' => $this->getAmountInteger(),
            'isFinal' => true,
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
