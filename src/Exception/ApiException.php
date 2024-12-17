<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class ApiException extends HttpException
{
    protected array $errors;

    public function __construct(
        string $message,
        array $errors = [],
        int $statusCode = 400
    ) {
        parent::__construct($statusCode, $message);
        $this->errors = $errors ?: [['message' => $message]];
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
