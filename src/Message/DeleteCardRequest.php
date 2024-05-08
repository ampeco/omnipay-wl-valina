<?php

namespace Ampeco\OmnipayWlValina\Message;

class DeleteCardRequest extends AbstractRequest
{
    public function getEndpoint(): string
    {
        return '/tokens';
    }

    public function getRequestMethod(): string
    {
        return 'DELETE';
    }

    /**
     * @inheritDoc
     */
    public function getData()
    {
        return [
            'token' => $this->getToken(),
        ];
    }

    protected function createResponse(array $data, int $statusCode): Response
    {
        return $this->response = new DeleteCardResponse($this, $data, $statusCode);
    }
}
