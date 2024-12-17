<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class PlayerLimitExceededException extends ApiException
{
    public function __construct(string $message = 'A team cannot have more than 11 players.')
    {
        parent::__construct($message);
    }
}
