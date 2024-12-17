<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class ValidationFailedException extends ApiException
{
    public function __construct(array $errors, int $statusCode = 400)
    {
        parent::__construct('Validation Failed', $errors, $statusCode);
    }
}
