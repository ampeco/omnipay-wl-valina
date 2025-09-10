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

        $data['cardPaymentMethodSpecificInput']['authorizationMode'] = 'FINAL_AUTHORIZATION';
        $data['cardPaymentMethodSpecificInput']['threeDSecure']['skipAuthentication'] = false;
        $data['cardPaymentMethodSpecificInput']['threeDSecure']['challengeIndicator'] = 'challenge-required';
        $data['cardPaymentMethodSpecificInput']['unscheduledCardOnFileRequestor'] = 'cardholderInitiated';
        $data['cardPaymentMethodSpecificInput']['unscheduledCardOnFileSequenceIndicator'] = 'first';
        $data['hostedTokenizationId'] = $this->getHostedTokenizationId();

        $threeDSReturnUrl = $this->getThreeDSReturnUrl();
        if ($threeDSReturnUrl) {
            $data['cardPaymentMethodSpecificInput']['threeDSecure']['redirectionData']['returnUrl'] = $threeDSReturnUrl;
        }

        $challengeCanvasSize = $this->getThreeDSChallengeCanvasSize();
        if ($challengeCanvasSize) {
            $data['cardPaymentMethodSpecificInput']['threeDSecure']['challengeCanvasSize'] = $challengeCanvasSize;
        }

        return $data;
    }

    public function getCustomerData(): array
    {
        $deviceData = [
            'acceptHeader' => $this->getAcceptHeader(),
            'browserData' => array_filter([
                'colorDepth' => $this->getColorDepth(),
                'javaEnabled' => true,
                'javaScriptEnabled' => true,
                'screenHeight' => $this->getScreenHeight(),
                'screenWidth' => $this->getScreenWidth(),
            ]),
            'ipAddress' => $this->getIpAddress(),
            'locale' => $this->getLocale(),
            'timezoneOffsetUtcMinutes' => $this->getTimezoneOffsetUtcMinutes(),
            'userAgent' => $this->getUserAgent(),
        ];

        if ($deviceData['ipAddress'] === null) {
            unset($deviceData['ipAddress']);
        }

        return [
            'customer' => [
                'device' => $deviceData,
                'contactDetails' => [
                    'emailAddress' => $this->getEmail(),
                ],
                'billingAddress' => [
                    'city' => $this->getCity(),
                    'countryCode' => $this->getCountryCode(),
                    'street' => $this->getStreet(),
                    'zip' => $this->getZip(),
                    'additionalInfo' => $this->getAdditionalInfo(),
                ],
            ],
        ];
    }
}
