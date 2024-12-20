<?php

namespace App\Exception;

use Exception;

class PlayerLimitExceededException extends Exception
{
    public function __construct(string $message = 'A team cannot have more than 11 players.')
    {
        parent::__construct($message);
    }
}
