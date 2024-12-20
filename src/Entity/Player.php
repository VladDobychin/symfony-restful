<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

#[ORM\Entity]
class Player
{
    #[ORM\ManyToOne(targetEntity: Team::class, inversedBy: 'players')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Team $team = null;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $firstName;

    #[ORM\Column(length: 255)]
    private string $lastName;

    #[ORM\Column]
    private int $age;

    #[ORM\Column(length: 255)]
    private string $position;


    public function __construct(Team $team, string $firstName, string $lastName, int $age, string $position)
    {
        if (empty($firstName) || empty($lastName) || empty($position)) {
            throw new InvalidArgumentException('Invalid player data.');
        }

        if (($age < 16) || ($age > 50)) {
            throw new InvalidArgumentException('Player must be between 16 and 50 years old.');
        }

        $this->team = $team;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->age = $age;
        $this->position = $position;
    }

    public function rename(string $firstName, string $lastName): void
    {
        if (empty($firstName) || empty($lastName)) {
            throw new InvalidArgumentException('Names cannot be empty.');
        }
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    public function changeAge(int $age): void
    {
        if ($age < 16) {
            throw new InvalidArgumentException('Player must be at least 16 years old.');
        }
        $this->age = $age;
    }

    public function changePosition(string $position): void
    {
        if (empty($position)) {
            throw new InvalidArgumentException('Position cannot be empty.');
        }
        $this->position = $position;
    }

    public function leaveTeam(): void
    {
        $this->team = null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

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

    public function getTeam(): Team
    {
        return $this->team;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'firstName' => $this->getFirstName(),
            'lastName' => $this->getLastName(),
            'age' => $this->getAge(),
            'position' => $this->getPosition(),
            'teamId' => $this->team?->getId()
        ];
    }
}
