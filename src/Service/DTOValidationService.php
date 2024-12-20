<?php

namespace App\Service;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class DTOValidationService
{
    public function __construct(private ValidatorInterface $validator)
    {
    }

    public function validate(mixed $dto, array $groups = null): array
    {
        $violations = $this->validator->validate($dto, null, $groups);

        if (count($violations) === 0) {
            return [];
        }

        return $this->formatErrors($violations);
    }

    private function formatErrors(ConstraintViolationListInterface $violations): array
    {
        $errors = [];

        foreach ($violations as $violation) {
            $errors[] = [
                'property' => $violation->getPropertyPath(),
                'value'    => $violation->getInvalidValue(),
                'message'  => $violation->getMessage(),
            ];
        }

        return $errors;
    }
}
