<?php

namespace App\Exception;

class PlayerNotFoundException extends ApiException
{
    public function __construct(string $message = 'Player not found')
    {
        parent::__construct($message);
    }
}
