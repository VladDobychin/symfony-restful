<?php

namespace App\Exception;

use Exception;

class PlayerNotFoundException extends Exception
{
    public function __construct(string $message = 'Player not found')
    {
        parent::__construct($message);
    }
}
