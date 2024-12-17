<?php

namespace App\Service;

use App\Entity\Team;
use App\Repository\TeamRepository;
use App\Request\{CreateTeamRequest, UpdateTeamRequest};
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

    public function createTeam(CreateTeamRequest $request): Team
    {
        $team = new Team();
        $team->setName($request->name)
            ->setCity($request->city)
            ->setYearFounded($request->yearFounded)
            ->setStadiumName($request->stadiumName);

        $this->entityManager->persist($team);
        $this->entityManager->flush();

        $this->logger->info('[Team] created successfully', [
            'id' => $team->getId(),
            'name' => $team->getName(),
            'city' => $team->getCity(),
            'yearFounded' => $team->getYearFounded(),
            'stadiumName' => $team->getStadiumName(),
        ]);

        return $team;
    }

    public function getAllTeams(): array
    {
        return $this->teamRepository
            ->findAllTeams();
    }

    public function getTeamById(int $id): ?Team
    {
        return $this->teamRepository->findTeamById($id);
    }

    public function updateTeam(int $id, UpdateTeamRequest $request): ?Team
    {
        $team = $this->teamRepository->findTeamById($id);

        if (!$team) {
            return null;
        }

        if (isset($request->name)) {
            $team->setName($request->name);
        }
        if (isset($request->city)) {
            $team->setCity($request->city);
        }
        if (isset($request->yearFounded)) {
            $team->setYearFounded((int)$request->yearFounded);
        }
        if (isset($request->stadiumName)) {
            $team->setStadiumName($request->stadiumName);
        }

        $this->logger->info('[Team] was updated successfully', [
            'id' => $team->getId(),
            'name' => $team->getName(),
            'city' => $team->getCity(),
            'yearFounded' => $team->getYearFounded(),
            'stadiumName' => $team->getStadiumName(),
        ]);

        $this->entityManager->flush();

        return $team;
    }
}