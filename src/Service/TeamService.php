<?php

namespace App\Service;

use App\Entity\Team;
use App\Repository\TeamRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class TeamService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private TeamRepository $teamRepository,
        private LoggerInterface $logger
    ) {
    }

    public function createTeam(string $name, string $city, int $yearFounded, string $stadiumName): Team
    {
        $team = new Team();
        $team->setName($name)
            ->setCity($city)
            ->setYearFounded($yearFounded)
            ->setStadiumName($stadiumName);

        $this->entityManager->persist($team);
        $this->entityManager->flush();

        $this->logger->info('[Team] created successfully', [
            'id' => $team->getId(),
            'name' => $name,
            'city' => $city,
            'yearFounded' => $yearFounded,
            'stadiumName' => $stadiumName,
        ]);

        return $team;
    }

    public function getAllTeams(): array
    {
        return $this->teamRepository
            ->findAllTeams();
    }
}