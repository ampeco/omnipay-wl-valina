<?php

namespace Ampeco\OmnipayWlValina\Message;

class InitialPurchaseRequest extends AuthorizeRequest
{
    public function getEndpoint(): string
    {
        return '/payments';
    }

    public function getRequestMethod(): string
    {
        return 'POST';
    }

    public function getData(): array
    {
        $data = parent::getData();

        $data['cardPaymentMethodSpecificInput']['threeDSecure']['skipAuthentication'] = false;
        $data['cardPaymentMethodSpecificInput']['threeDSecure']['challengeIndicator'] = 'challenge-required';
        $data['cardPaymentMethodSpecificInput']['unscheduledCardOnFileRequestor'] = 'cardholderInitiated';
        $data['cardPaymentMethodSpecificInput']['unscheduledCardOnFileSequenceIndicator'] = 'first';
        $data['hostedTokenizationId'] = $this->getHostedTokenizationId();

        return $data;
    }

    public function getCustomerData(): array
    {
        return [
            'customer' => [
                'device' => [
                    'acceptHeader' => $this->getAcceptHeader(),
                    'browserData' => [
                        'colorDepth' => $this->getColorDepth(),
                        'javaEnabled' => true,
                        'javaScriptEnabled' => true,
                        'screenHeight' => $this->getScreenHeight(),
                        'screenWidth' => $this->getScreenWidth(),
                    ],
                    'ipAddress' => $this->getIpAddress(),
                    'locale' => $this->getLocale(),
                    'timezoneOffsetUtcMinutes' => $this->getTimezoneOffsetUtcMinutes(),
                    'userAgent' => $this->getUserAgent(),
                ],
                'contactDetails' => [
                    'emailAddress' => $this->getEmail(),
                ],
                'billingAddress' => [
                    'city' => $this->getCity(),
                    'countryCode' => $this->getCountryCode(),
                    'street' => $this->getStreet(),
                ],
            ],
        ];
    }
}
