<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class TeamDTO
{
    #[Assert\NotBlank(groups: ['create'])]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: 'The name must be at least {{ limit }} characters long.',
        maxMessage: 'The name cannot exceed {{ limit }} characters.',
        groups: ['create', 'update']
    )]
    #[Assert\Type(
        type: 'string',
        groups: ['create', 'update']
    )] #[Assert\Regex(
        pattern: "/^[a-zA-Z\s'-]+$/",
        message: 'The name must only contain letters, spaces, apostrophes, and hyphens.',
        groups: ['create', 'update']
    )]
    private ?string $name;

    #[Assert\NotBlank(groups: ['create'])]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: 'The city must be at least {{ limit }} characters long.',
        maxMessage: 'The city cannot exceed {{ limit }} characters.',
        groups: ['create', 'update']
    )]
    #[Assert\Regex(
        pattern: "/^[a-zA-Z\s'-]+$/",
        message: 'The city must only contain letters, spaces, apostrophes, and hyphens.',
        groups: ['create', 'update']
    )]
    private ?string $city;

    #[Assert\NotBlank(groups: ['create'])]
    #[Assert\Length(
        min: 4,
        max: 4,
        exactMessage: 'The year must be exactly {{ limit }} digits long.',
        groups: ['create', 'update']
    )]
    #[Assert\Regex(
        pattern: '/^\d{4}$/',
        message: 'The year must contain only numbers.',
        groups: ['create', 'update']
    )]
    #[Assert\Range(
        notInRangeMessage: 'The year must be between {{ min }} and {{ max }}.',
        min: 1850,
        max: 2024,
        groups: ['create', 'update']
    )]
    private ?string $yearFounded;

    #[Assert\NotBlank(groups: ['create'])]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: 'The stadium name must be at least {{ limit }} characters long.',
        maxMessage: 'The stadium name cannot exceed {{ limit }} characters.',
        groups: ['create', 'update']
    )]
    #[Assert\Regex(
        pattern: "/^[a-zA-Z\s'-]+$/",
        message: 'The stadium name must only contain letters, spaces, apostrophes, and hyphens.',
        groups: ['create', 'update']
    )]
    private ?string $stadiumName;

    private function __construct(
        ?string $name,
        ?string $city,
        ?string $yearFounded,
        ?string $stadiumName
    ) {
        $this->name = $name;
        $this->city = $city;
        $this->yearFounded = $yearFounded;
        $this->stadiumName = $stadiumName;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['name'] ?? null,
            $data['city'] ?? null,
            $data['yearFounded'] ?? null,
            $data['stadiumName'] ?? null
        );
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function getYearFounded(): ?int
    {
        return $this->yearFounded !== null ? (int) $this->yearFounded : null;
    }

    public function getStadiumName(): ?string
    {
        return $this->stadiumName;
    }
}
