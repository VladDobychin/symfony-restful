<?php

namespace App\Service;

use App\DTO\TeamDataInterface;
use App\Entity\Team;
use App\Event\TeamRelocatedEvent;
use App\Exception\TeamNotFoundException;
use App\Repository\TeamRepository;
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

    public function createTeam(TeamDataInterface $request): Team
    {
        $team = new Team();
        $team->setName($request->getName())
            ->setCity($request->getCity())
            ->setYearFounded($request->getYearFounded())
            ->setStadiumName($request->getStadiumName());

        $this->entityManager->persist($team);
        $this->entityManager->flush();

        $this->logger->info('[Team] created successfully', $team->toArray());

        return $team;
    }

    public function getAllTeams(): array
    {
        $teams = $this->teamRepository
            ->findAllTeams();

        return array_map(fn($team) => $team->toArray(), $teams);
    }

    public function getTeamById(int $id): Team
    {
        $team = $this->teamRepository->findTeamById($id);

        if (!$team) {
            throw new TeamNotFoundException("Team with ID {$id} not found.");
        }

        return $team;
    }

    // TODO: handle case when no fields are supplied
    public function updateTeam(int $id, TeamDataInterface $request): Team
    {
        $team = $this->getTeamById($id);

        $oldCity = $team->getCity();

        $this->applyTeamUpdates($team, $request);

        $this->entityManager->flush();

        $this->logger->info('[Team] was updated successfully', $team->toArray());

        if ($team->getCity() !== $oldCity) {
            $this->eventDispatcher->dispatch(new TeamRelocatedEvent($team->getId(), $oldCity));
        }

        return $team;
    }

    public function deleteTeam(int $id): void
    {
        $team = $this->getTeamById($id);

        $this->logger->info("Deleting team '{$team->getName()}' with ID: {$id}");

        $this->teamRepository->deleteTeam($team);

        $this->logger->info("Team '{$team->getName()}' with ID: {$id} has been deleted successfully");
    }

    private function applyTeamUpdates(Team $team, TeamDataInterface $request): void
    {
        if ($request->getName() !== null) {
            $team->setName($request->getName());
        }
        if ($request->getCity() !== null) {
            $team->setCity($request->getCity());
        }
        if ($request->getYearFounded() !== null) {
            $team->setYearFounded($request->getYearFounded());
        }
        if ($request->getStadiumName() !== null) {
            $team->setStadiumName($request->getStadiumName());
        }
    }
}