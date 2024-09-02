<?php

namespace Ampeco\OmnipayWlValina\Message;

class GetTokenRequest extends AbstractRequest {

    public function getEndpoint(): string
    {
        return '/tokens' . '/' . $this->getToken();
    }

    public function getRequestMethod(): string
    {
       return 'GET';
    }

    protected function createResponse(array $data, int $statusCode): Response
    {
        return $this->response = new GetTokenResponse($this, $data, $statusCode);
    }
}
