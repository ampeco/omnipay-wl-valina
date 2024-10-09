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
}
