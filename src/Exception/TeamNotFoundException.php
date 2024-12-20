<?php

namespace App\Exception;

use Exception;

class TeamNotFoundException extends Exception
{
    public function __construct(string $message = 'Team with such id was not found')
    {
        parent::__construct($message);
    }
}
