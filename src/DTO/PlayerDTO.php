<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class PlayerDTO
{
    #[Assert\NotBlank(groups: ['create'])]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: 'First name must be at least {{ limit }} characters long.',
        maxMessage: 'First name cannot exceed {{ limit }} characters.',
        groups: ['create', 'update']
    )]
    #[Assert\Regex(
        pattern: "/^[a-zA-Z\s'-]+$/",
        message: 'The first name must only contain letters, spaces, apostrophes, and hyphens.',
        groups: ['create', 'update']
    )]
    private ?string $firstName;

    #[Assert\NotBlank(groups: ['create'])]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: 'Last name must be at least {{ limit }} characters long.',
        maxMessage: 'Last name cannot exceed {{ limit }} characters.',
        groups: ['create', 'update']
    )]
    #[Assert\Regex(
        pattern: "/^[a-zA-Z\s'-]+$/",
        message: 'The last name must only contain letters, spaces, apostrophes, and hyphens.',
        groups: ['create', 'update']
    )]
    private ?string $lastName;

    #[Assert\NotBlank(groups: ['create'])]
    #[Assert\Range(
        notInRangeMessage: 'Age must be between {{ min }} and {{ max }}.',
        min: 16,
        max: 50,
        groups: ['create', 'update']
    )]
    private ?int $age;

    #[Assert\NotBlank(groups: ['create'])]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: 'Position must be at least {{ limit }} characters long.',
        maxMessage: 'Position cannot exceed {{ limit }} characters.',
        groups: ['create', 'update']
    )]
    private ?string $position;

    #[Assert\NotBlank(groups: ['create'])]
    #[Assert\Type(type: 'integer', groups: ['create', 'update'])]
    #[Assert\Positive(message: 'Team ID must be a positive number.', groups: ['create', 'update'])]
    private ?int $teamId;

    private function __construct(?string $firstName, ?string $lastName, ?int $age, ?string $position, ?int $teamId)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->age = $age;
        $this->position = $position;
        $this->teamId = $teamId;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['firstName'] ?? null,
            $data['lastName'] ?? null,
            isset($data['age']) ? (int)$data['age'] : null,
            $data['position'] ?? null,
            isset($data['teamId']) ? (int)$data['teamId'] : null
        );
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function getTeamId(): ?int
    {
        return $this->teamId;
    }
}
