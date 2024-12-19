<?php

namespace App\Exception;

class TeamNotFoundException extends ApiException
{
    public function __construct(string $message = 'Team with such id was not found')
    {
        parent::__construct($message);
    }
}
