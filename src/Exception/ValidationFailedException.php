<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class ValidationFailedException extends HttpException
{
    private array $errors;

    public function __construct(array $errors, int $statusCode = 400)
    {
        parent::__construct($statusCode, 'Validation Failed');
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
