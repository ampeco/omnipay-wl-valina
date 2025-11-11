<?php

namespace Ampeco\OmnipayWlValina\Message;

class GetPaymentDetailsRequest extends AbstractRequest
{
    public function getEndpoint(): string
    {
        return '/payments' . '/' . $this->getPaymentId() . '/details';
    }

    public function getRequestMethod(): string
    {
        return 'GET';
    }

    public function createResponse(array $data, int $statusCode): Response
    {
        return $this->response = new GetPaymentResponse($this, $data, $statusCode);
    }
}
