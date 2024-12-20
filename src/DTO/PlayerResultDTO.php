<?php

namespace App\DTO;

class PlayerResultDTO
{
    public function __construct(
        private int $id,
        private string $firstName,
        private string $lastName,
        private int $age,
        private string $position,
        private int $teamId
    ) {
    }

    public static function fromEntity(\App\Entity\Player $player): self
    {
        return new self(
            $player->getId(),
            $player->getFirstName(),
            $player->getLastName(),
            $player->getAge(),
            $player->getPosition(),
            $player->getTeam()->getId(),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'age' => $this->age,
            'position' => $this->position,
            'teamId' => $this->teamId,
        ];
    }
}
