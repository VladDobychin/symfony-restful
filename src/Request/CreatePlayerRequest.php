<?php

namespace App\Request;

use Symfony\Component\Validator\Constraints as Assert;

class CreatePlayerRequest extends AbstractJsonRequest
{
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: 'First name must be at least {{ limit }} characters long.',
        maxMessage: 'First name cannot exceed {{ limit }} characters.'
    )]
    #[Assert\Regex(
        pattern: "/^[a-zA-Z\s'-]+$/",
        message: 'The first name must only contain letters, spaces, apostrophes, and hyphens.'
    )]
    public readonly string $firstName;

    #[Assert\NotBlank]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: 'Last name must be at least {{ limit }} characters long.',
        maxMessage: 'Last name cannot exceed {{ limit }} characters.'
    )]
    #[Assert\Regex(
        pattern: "/^[a-zA-Z\s'-]+$/",
        message: 'The last name must only contain letters, spaces, apostrophes, and hyphens.'
    )]
    public readonly string $lastName;

    #[Assert\NotBlank]
    #[Assert\Range(
        notInRangeMessage: 'Age must be between {{ min }} and {{ max }}.',
        min: 16,
        max: 50
    )]
    public readonly int $age;

    // TODO: Implement position value object to have real positions like 'goalkeeper', 'striker' etc
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: 'Position must be at least {{ limit }} characters long.',
        maxMessage: 'Position cannot exceed {{ limit }} characters.'
    )]
    public readonly string $position;

    #[Assert\NotBlank]
    #[Assert\Type('integer')]
    #[Assert\Positive(message: 'Team ID must be a positive number.')]
    public readonly int $teamId;
}
