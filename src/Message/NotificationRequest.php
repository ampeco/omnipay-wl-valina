<?php

namespace Ampeco\OmnipayWlValina\Message;

class NotificationRequest extends AbstractRequest
{
    public function getEndpoint(): string
    {
        return '/hostedtokenizations';
    }

    public function getRequestMethod(): string
    {
        return 'GET';
    }

    /**
     * @inheritDoc
     */
    public function getData()
    {
        return [];
    }

    public function sendData($data): Response
    {
        $hostedTokenizationId = $data['hostedTokenizationId'] ?? '';
        $requestMethod = $this->getRequestMethod();

        $url = $this->getBaseUrl() . $this->getMerchantId() . $this->getEndpoint() . '/' . $hostedTokenizationId;
        $headersArray = $this->getHeaders($this->getEndpoint() . '/' . $hostedTokenizationId, $requestMethod);
        $headers = [
            'Content-Type: ' . $headersArray['Content-Type'],
            'Authorization: ' . $headersArray['Authorization'],
            'Date: ' . $headersArray['Date'],
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $responseContent = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE) ?: '';
        curl_close($ch);

        $responseData = $this->parseJsonResponse($responseContent, $contentType, $statusCode);

        return $this->createResponse($responseData, $statusCode);
    }

    protected function createResponse(array $data, int $statusCode): Response
    {
        return new NotificationResponse($this, $data, $statusCode);
    }
}
