<?php

namespace App\Service;

use App\DTO\TeamDTO;
use App\DTO\TeamResultDTO;
use App\Entity\Team;
use App\Exception\PlayerNotFoundException;
use App\Exception\TeamNotFoundException;
use App\Repository\TeamRepository;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class TeamService
{
    public function __construct(
        private TeamRepository $teamRepository,
        private LoggerInterface $logger,
        private EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function createTeam(TeamDTO $teamData): TeamResultDTO
    {
        $team = new Team(
            $teamData->getName(),
            $teamData->getCity(),
            $teamData->getYearFounded(),
            $teamData->getStadiumName()
        );

        $this->teamRepository->saveTeam($team);

        $this->logger->info('[Team] created successfully', $team->toArray());

        return TeamResultDTO::fromEntity($team);
    }

    public function getAllTeams(): array
    {
        $teams = $this->teamRepository
            ->findAllTeams();

        return array_map(
            fn($team) => TeamResultDTO::fromEntity($team)->toArray(),
            $teams
        );
    }

    /**
     * @throws TeamNotFoundException
     */
    public function getTeamById(int $id): Team
    {
        $team = $this->teamRepository->findTeamById($id);

        if (!$team) {
            throw new TeamNotFoundException("Team with ID {$id} not found.");
        }

        return $team;
    }

    /**
     * @throws TeamNotFoundException
     */
    public function getTeamByIdDTO(int $id): TeamResultDTO
    {
        $team = $this->getTeamById($id);
        return TeamResultDTO::fromEntity($team);
    }

    /**
     * @throws TeamNotFoundException
     */
    public function updateTeam(int $id, TeamDTO $teamData): TeamResultDTO
    {
        $team = $this->getTeamById($id);

        $this->applyTeamUpdates($team, $teamData);

        $this->teamRepository->saveTeam($team);

        $this->logger->info('[Team] was updated successfully', $team->toArray());

        return TeamResultDTO::fromEntity($team);
    }

    /**
     * @throws PlayerNotFoundException
     */
    public function getTeamByPlayerId(int $playerId): Team
    {
        $team = $this->teamRepository->findTeamByPlayerId($playerId);
        if (!$team) {
            throw new PlayerNotFoundException("Player with id $playerId not found");
        }

        return $team;
    }

    /**
     * @throws TeamNotFoundException
     */
    public function deleteTeam(int $id): void
    {
        $team = $this->getTeamById($id);

        $this->logger->info("Deleting team '{$team->getName()}' with ID: {$id}");

        $this->teamRepository->deleteTeam($team);

        $this->logger->info("Team '{$team->getName()}' with ID: {$id} has been deleted successfully");
    }

    private function applyTeamUpdates(Team $team, TeamDTO $teamData): void
    {
        if ($teamData->getName() !== null) {
            $team->renameTeam($teamData->getName());
        }

        if ($teamData->getCity() !== null ) {
            $team->relocateTeam($teamData->getCity());
        }

        if ($teamData->getStadiumName() !== null ) {
            $team->changeStadium($teamData->getStadiumName());
        }

        if ($teamData->getYearFounded() !== null) {
            $team->changeYearFounded($teamData->getYearFounded());
        }
    }

    public function updateTeamAggregate(Team $team): Team
    {
        $this->teamRepository->saveTeam($team);

        foreach ($team->popEvents() as $event) {
            $this->eventDispatcher->dispatch($event);
        }

        return $team;
    }
}