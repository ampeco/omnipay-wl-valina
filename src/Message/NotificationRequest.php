<?php

namespace Ampeco\OmnipayWlValina\Message;

use Illuminate\Support\Facades\Log;
use JsonException;

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
        $responseBody = curl_exec($ch);
        $statusCode = (int) curl_getinfo($ch, \CURLINFO_RESPONSE_CODE);
        $contentType = curl_getinfo($ch, \CURLINFO_CONTENT_TYPE) ?: '';
        curl_close($ch);

        $isJsonResponse = str_contains(strtolower($contentType), 'application/json')
            || str_contains(strtolower($contentType), 'text/json');

        if (!$isJsonResponse && !empty($responseBody)) {
            Log::warning('[Worldline] Non-JSON notification response received', [
                'content_type' => $contentType,
                'status_code' => $statusCode,
                'response_preview' => strlen($responseBody) > 1000
                    ? substr($responseBody, 0, 1000) . '...'
                    : $responseBody,
            ]);

            return $this->createResponse([
                'errors' => [
                    [
                        'code' => 'GATEWAY_ERROR',
                        'message' => 'Invalid response from payment gateway',
                        'httpStatusCode' => $statusCode,
                    ]
                ],
            ], $statusCode);
        }

        try {
            $responseData = json_decode($responseBody, true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            Log::warning('[Worldline] JSON parsing error in notification', [
                'json_error' => $e->getMessage(),
                'content_type' => $contentType,
                'status_code' => $statusCode,
                'response' => strlen($responseBody) > 5000
                    ? substr($responseBody, 0, 5000) . '...'
                    : $responseBody,
            ]);

            return $this->createResponse([
                'errors' => [
                    [
                        'code' => 'JSON_PARSE_ERROR',
                        'message' => 'Failed to parse gateway response',
                        'httpStatusCode' => $statusCode,
                    ]
                ],
            ], $statusCode);
        }

        return $this->createResponse($responseData, $statusCode);
    }

    protected function createResponse(array $data, int $statusCode): Response
    {
        return new NotificationResponse($this, $data, $statusCode);
    }
}
