<?php

namespace App\Request;

use App\DTO\PlayerDataInterface;
use Symfony\Component\Validator\Constraints as Assert;

class UpdatePlayerRequest extends AbstractJsonRequest implements PlayerDataInterface
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
    private readonly ?string $firstName;

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
    private readonly ?string $lastName;

    #[Assert\Range(
        notInRangeMessage: 'The age must be between {{ min }} and {{ max }}.',
        min: 16,
        max: 50
    )]
    private readonly ?int $age;

    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: 'The position must be at least {{ limit }} characters long.',
        maxMessage: 'The position cannot exceed {{ limit }} characters.'
    )]
    private readonly ?string $position;


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

    public function getTeamId(): ?int
    {
        return null;
    }
}
