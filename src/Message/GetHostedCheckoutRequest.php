<?php

declare(strict_types=1);

namespace Ampeco\OmnipayWlValina\Message;

class GetHostedCheckoutRequest extends AbstractRequest
{
    public function getEndpoint(): string
    {
        return '/hostedcheckouts/' . $this->getHostedCheckoutId();
    }

    public function getRequestMethod(): string
    {
        return 'GET';
    }

    public function getData(): array
    {
        return [];
    }

    protected function createResponse(array $data, int $statusCode): Response
    {
        return $this->response = new GetHostedCheckoutResponse($this, $data, $statusCode);
    }
}
