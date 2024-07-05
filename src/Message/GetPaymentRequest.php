<?php

namespace Ampeco\OmnipayWlValina\Message;

class GetPaymentRequest extends AbstractRequest {

    public function getEndpoint(): string
    {
        return '/payments' . '/' . $this->getPaymentId();
    }

    public function getRequestMethod(): string
    {
        return 'GET';
    }

    protected function createResponse(array $data, int $statusCode): Response
    {
        return $this->response = new GetPaymentResponse($this, $data, $statusCode);
    }
}
