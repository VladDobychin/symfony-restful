<?php

namespace App\Exception;

class PlayerLimitExceededException extends ApiException
{
    public function __construct(string $message = 'A team cannot have more than 11 players.')
    {
        parent::__construct($message);
    }
}
