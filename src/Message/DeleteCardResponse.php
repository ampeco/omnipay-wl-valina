<?php

namespace Ampeco\OmnipayWlValina\Message;

class DeleteCardResponse extends Response
{
    public function isSuccessful() : bool
    {
        return $this->code === 204;
    }

    public function getMessage(): ?string
    {
        return parent::getMessage() ?: 'Card deleted';
    }
}
