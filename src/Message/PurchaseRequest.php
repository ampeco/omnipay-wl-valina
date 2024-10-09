<?php

namespace Ampeco\OmnipayWlValina\Message;

class PurchaseRequest extends AuthorizeRequest
{
    public function getData(): array
    {
        $data = parent::getData();

        // fake that all purchases are ev driver initiated, hoping the response will contain 3DS challenge redirect url that we can handle
        // TODO: If this doesn't work after test on QA, delete it or change it to merchantInitiated
        $data['cardPaymentMethodSpecificInput']['unscheduledCardOnFileRequestor'] = 'cardholderInitiated';
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
