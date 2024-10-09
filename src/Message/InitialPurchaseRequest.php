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
        $data['cardPaymentMethodSpecificInput']['threeDSecure']['challengeIndicator'] = 'challenge-requested';
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
                    //TODO: Leave the old ones in case the dynamic ones are not working
                    //                        'acceptHeader' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
                    //                        'userAgent' => 'Mozilla/5.0(WindowsNT10.0;Win64;x64)AppleWebKit/537.36(KHTML,likeGecko)Chrome/75.0.3770.142Safari/537.36',
                    'acceptHeader' => $this->getAcceptHeader(),
                    'userAgent' => $this->getUserAgent(),
                    'locale' => $this->getLocale(),
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
