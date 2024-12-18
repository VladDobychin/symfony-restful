<?php

namespace App\Request;

use App\DTO\PlayerDataInterface;
use Symfony\Component\Validator\Constraints as Assert;

class CreatePlayerRequest extends AbstractJsonRequest implements PlayerDataInterface
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
    private readonly string $firstName;

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
    private readonly string $lastName;

    #[Assert\NotBlank]
    #[Assert\Range(
        notInRangeMessage: 'Age must be between {{ min }} and {{ max }}.',
        min: 16,
        max: 50
    )]
    private readonly int $age;

    // TODO: Implement position value object to have real positions like 'goalkeeper', 'striker' etc
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: 'Position must be at least {{ limit }} characters long.',
        maxMessage: 'Position cannot exceed {{ limit }} characters.'
    )]
    private readonly string $position;

    #[Assert\NotBlank]
    #[Assert\Type('integer')]
    #[Assert\Positive(message: 'Team ID must be a positive number.')]
    private readonly int $teamId;

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getAge(): int
    {
        return $this->age;
    }

    public function getPosition(): string
    {
        return $this->position;
    }

    public function getTeamId(): int
    {
        return $this->teamId;
    }
}
