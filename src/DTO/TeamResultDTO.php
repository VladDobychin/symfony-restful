<?php

namespace App\DTO;

use App\Entity\Team;

class TeamResultDTO
{
    public function __construct(
        private int $id,
        private string $name,
        private string $city,
        private int $yearFounded,
        private string $stadiumName
    ) {
    }

    public static function fromEntity(Team $team): self
    {
        return new self(
            $team->getId(),
            $team->getName(),
            $team->getCity(),
            $team->getYearFounded(),
            $team->getStadiumName()
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'city' => $this->city,
            'yearFounded' => $this->yearFounded,
            'stadiumName' => $this->stadiumName
        ];
    }
}
