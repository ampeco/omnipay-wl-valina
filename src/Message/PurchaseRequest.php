<?php

namespace Ampeco\OmnipayWlValina\Message;

class PurchaseRequest extends AuthorizeRequest
{
    public function getData(): array
    {
        $data = parent::getData();

        $data['cardPaymentMethodSpecificInput']['unscheduledCardOnFileRequestor'] = 'merchantInitiated';
        $data['cardPaymentMethodSpecificInput']['unscheduledCardOnFileSequenceIndicator'] = 'subsequent';
        $data['cardPaymentMethodSpecificInput']['authorizationMode'] = 'SALE';
        // TODO: try with no-challenge-requested-consumer-authentication-performed after David's feedback
        $data['cardPaymentMethodSpecificInput']['threeDSecure']['challengeIndicator'] = 'no-challenge-requested';
        $data['cardPaymentMethodSpecificInput']['threeDSecure']['redirectionData']['returnUrl'] = $this->getReturnUrl();
        $data['cardPaymentMethodSpecificInput']['threeDSecure']['skipAuthentication'] = true;

        return $data;
    }

    protected function createResponse(array $data, int $statusCode): Response
    {
        return $this->response = new CreatePaymentResponse($this, $data, $statusCode);
    }
}
