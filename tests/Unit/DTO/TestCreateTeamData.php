<?php

namespace App\Tests\Unit\DTO;

use App\DTO\TeamDataInterface;

class TestCreateTeamData implements TeamDataInterface
{
    public function __construct(
        private string $name,
        private string $city,
        private int $yearFounded,
        private string $stadiumName
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getYearFounded(): int
    {
        return $this->yearFounded;
    }

    public function getStadiumName(): string
    {
        return $this->stadiumName;
    }
}
