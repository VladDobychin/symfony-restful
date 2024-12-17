<?php

namespace App\Service;

use App\Entity\Team;
use App\Event\TeamRelocatedEvent;
use App\Repository\TeamRepository;
use App\Request\{CreateTeamRequest, UpdateTeamRequest};
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class TeamService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private TeamRepository $teamRepository,
        private LoggerInterface $logger,
        private EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function createTeam(CreateTeamRequest $request): Team
    {
        $team = new Team();
        $team->setName($request->name)
            ->setCity($request->city)
            ->setYearFounded((int)$request->yearFounded)
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

        $oldCity = $team->getCity();

        $this->applyTeamUpdates($team, $request);

        $this->entityManager->flush();

        $this->logger->info('[Team] was updated successfully', [
            'id' => $team->getId(),
            'name' => $team->getName(),
            'city' => $team->getCity(),
            'yearFounded' => $team->getYearFounded(),
            'stadiumName' => $team->getStadiumName(),
        ]);

        if ($team->getCity() !== $oldCity) {
            $this->eventDispatcher->dispatch(new TeamRelocatedEvent($team->getId(), $oldCity));
        }

        return $team;
    }

    public function deleteTeam(int $id): bool
    {
        $team = $this->teamRepository->findTeamById($id);

        if (!$team) {
            $this->logger->warning("Attempted to delete non-existent team with ID: {$id}");
            return false;
        }

        $this->logger->info("Deleting team '{$team->getName()}' with ID: {$id}");

        $this->teamRepository->deleteTeam($team);

        $this->logger->info("Team '{$team->getName()}' with ID: {$id} has been deleted successfully");

        return true;
    }

    private function applyTeamUpdates(Team $team, UpdateTeamRequest $request): void
    {
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
    }
}