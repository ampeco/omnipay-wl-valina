<?php

namespace Ampeco\OmnipayWlValina\Message;

use Ampeco\OmnipayWlValina\CommonParameters;
use Ampeco\OmnipayWlValina\Gateway;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Facades\Log;
use Omnipay\Common\Message\AbstractRequest as OmniPayAbstractRequest;

abstract class AbstractRequest extends OmniPayAbstractRequest
{
    use CommonParameters;

    const API_URL_PROD = 'https://payment.direct.worldline-solutions.com/v2/';
    const API_URL_TEST = 'https://payment.preprod.direct.worldline-solutions.com/v2/';

    protected ?Gateway $gateway;

    abstract public function getEndpoint(): string;

    abstract public function getRequestMethod(): string;

    abstract protected function createResponse(array $data, int $statusCode): Response;

    public function setGateway(Gateway $gateway): static
    {
        $this->gateway = $gateway;

        return $this;
    }

    public function getBaseUrl(): string
    {
        return $this->getTestMode() ? static::API_URL_TEST : static::API_URL_PROD;
    }

    /**
     * @throws \Exception
     */
    protected function getHeaders(string $endpoint, $requestMethod): array
    {
        $contentType = in_array($requestMethod, ['GET', 'DELETE']) ? '' : 'application/json';

        return array_merge($this->signHeaders($endpoint, $requestMethod), [
            'Content-Type' => $contentType,
        ]);
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function sendData($data): Response
    {
        $requestMethod = $this->getRequestMethod();

        $url = $this->getEndpoint();

        // Append token to URL for DELETE requests
        $url .= $requestMethod === 'DELETE' ? '/' . $data['token'] : '';

        // Prepare headers
        $headers = $this->getHeaders($url, $requestMethod);

        // Set request body; empty for DELETE
        $body = $requestMethod === 'DELETE' ? '' : json_encode($data);

        // Send request
        $response = $this->httpClient->request($requestMethod, $this->getBaseUrl() . $this->getMerchantId() . $url, $headers, $body);

        if ($requestMethod === 'DELETE') {
            $responseData = [];
        } else {
            $responseContent = $response->getBody()->getContents();
            $contentType = $response->getHeader('Content-Type')[0] ?? '';
            
            // Check if response is JSON based on Content-Type header
            $isJsonResponse = str_contains(strtolower($contentType), 'application/json') 
                || str_contains(strtolower($contentType), 'text/json');
            
            if (!$isJsonResponse && !empty($responseContent)) {
                // Log non-JSON response for debugging
                Log::warning('[Worldline] Non-JSON response received', [
                    'content_type' => $contentType,
                    'status_code' => $response->getStatusCode(),
                    'response_preview' => strlen($responseContent) > 1000 
                        ? substr($responseContent, 0, 1000) . '...' 
                        : $responseContent,
                ]);
                
                // Create error response data structure for non-JSON responses
                $responseData = [
                    'errors' => [
                        [
                            'code' => 'GATEWAY_ERROR',
                            'message' => 'Invalid response from payment gateway',
                            'httpStatusCode' => $response->getStatusCode(),
                        ]
                    ],
                ];
            } else {
                try {
                    $responseData = json_decode($responseContent, true, flags: JSON_THROW_ON_ERROR);
                } catch (\JsonException $e) {
                    Log::warning('[Worldline] JSON parsing error', [
                        'json_error' => $e->getMessage(),
                        'content_type' => $contentType,
                        'status_code' => $response->getStatusCode(),
                        'response' => strlen($responseContent) > 5_000
                            ? substr($responseContent, 0, 5_000) . '...'
                            : $responseContent,
                    ]);
                    
                    // Create error response data structure for JSON parsing errors
                    $responseData = [
                        'errors' => [
                            [
                                'code' => 'JSON_PARSE_ERROR',
                                'message' => 'Failed to parse gateway response',
                                'httpStatusCode' => $response->getStatusCode(),
                            ]
                        ],
                    ];
                }
            }
        }

        return $this->createResponse($responseData, $response->getStatusCode());
    }

    /**
     * Used for logging only to get the method and endpoint of the request
     */
    public function getEndpointLogData()
    {
        return [
            'method' => $this->getRequestMethod(),
            'url' => $this->getBaseUrl() . $this->getMerchantId() . $this->getEndpoint(),
        ];
    }

    /**
     * @inheritDoc
     */
    public function getData()
    {
        return [
            'merchantId' => $this->getMerchantId(),
            'api_key' => $this->getParameter('api_key'),
            'api_secret' => $this->getParameter('api_secret'),
        ];
    }

    /**
     * @throws \Exception
     */
    public function signHeaders(string $endpoint, string $requestMethod): array
    {
        $tz = new DateTimeZone('GMT');
        $dt = new DateTime('now', $tz);
        $dateTime = $dt->format('D, d M Y H:i:s T');

        return [
            'Authorization' => $this->createAuthorization($endpoint, $requestMethod, $dateTime),
            'Date' => $dateTime,
        ];
    }

    private function createAuthorization(string $endpoint, string $requestMethod, string $dateTime): string
    {
        [$merchantId, $apiKey, $apiSecret] = $this->signSettings();
        $contentType = in_array($requestMethod, ['GET', 'DELETE']) ? '' : 'application/json';
        $endpointUrl = '/v2/' . $merchantId . $endpoint;
        $stringToHash = $requestMethod . "\n" . $contentType . "\n" . $dateTime . "\n" . $endpointUrl . "\n";
        // Convert stringToHash + key into byte array
        $hash = hash_hmac('sha256', $stringToHash, $apiSecret, true);
        $encodedSignature = base64_encode($hash);

        return 'GCS v1HMAC:' . $apiKey . ':' . $encodedSignature;
    }

    private function signSettings(): array
    {
        $apiKey = $this->getApiKey();
        $apiSecret = $this->getApiSecret();
        $merchantId = $this->getMerchantId();

        return [$merchantId, $apiKey, $apiSecret];
    }
}
