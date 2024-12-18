<?php

namespace App\Tests\Unit\DTO;

use App\DTO\PlayerDataInterface;

class TestCreatePlayerData implements PlayerDataInterface
{
    public function __construct(
        private string $firstName,
        private string $lastName,
        private int $age,
        private string $position,
        private $teamId
    ) {}

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
