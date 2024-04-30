<?php

namespace Ampeco\OmnipayWlValina\Message;

use Ampeco\OmnipayWlValina\CommonParameters;
use Ampeco\OmnipayWlValina\Gateway;
use DateTime;
use DateTimeZone;
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
        $contentType = ($requestMethod !== 'GET') ? 'application/json' : '';

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
        $response = $this->httpClient->request(
            $requestMethod,
            $this->getBaseUrl() . $this->getMerchantId() . $this->getEndpoint(),
            $this->getHeaders($this->getEndpoint(), $requestMethod),
            json_encode($data),
        );
        return $this->createResponse(
            json_decode($response->getBody()->getContents(), true, flags: JSON_THROW_ON_ERROR),
            $response->getStatusCode(),
        );
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
        $contentType = ($requestMethod !== 'GET') ? 'application/json' : '';
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
