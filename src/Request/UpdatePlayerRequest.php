<?php

namespace App\Request;

use Symfony\Component\Validator\Constraints as Assert;

class UpdatePlayerRequest extends AbstractJsonRequest
{
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: 'The first name must be at least {{ limit }} characters long.',
        maxMessage: 'The first name cannot exceed {{ limit }} characters.'
    )]
    #[Assert\Regex(
        pattern: "/^[a-zA-Z\s'-]+$/",
        message: 'The first name must only contain letters, spaces, apostrophes, and hyphens.'
    )]
    public readonly ?string $firstName;

    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: 'The last name must be at least {{ limit }} characters long.',
        maxMessage: 'The last name cannot exceed {{ limit }} characters.'
    )]
    #[Assert\Regex(
        pattern: "/^[a-zA-Z\s'-]+$/",
        message: 'The last name must only contain letters, spaces, apostrophes, and hyphens.'
    )]
    public readonly ?string $lastName;

    #[Assert\Range(
        notInRangeMessage: 'The age must be between {{ min }} and {{ max }}.',
        min: 16,
        max: 50
    )]
    public readonly ?int $age;

    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: 'The position must be at least {{ limit }} characters long.',
        maxMessage: 'The position cannot exceed {{ limit }} characters.'
    )]
    public readonly ?string $position;
}
