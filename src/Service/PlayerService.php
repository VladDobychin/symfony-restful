<?php

namespace App\Service;

use App\DTO\PlayerDTO;
use App\Entity\Player;
use App\Exception\PlayerLimitExceededException;
use App\Exception\PlayerNotFoundException;
use App\Exception\TeamNotFoundException;
use LogicException;
use Psr\Log\LoggerInterface;

class PlayerService
{
    public function __construct(
        private TeamService $teamService,
        private LoggerInterface $logger
    ) {
    }

    /**
     * @throws TeamNotFoundException
     * @throws PlayerLimitExceededException
     */
    public function createPlayer(PlayerDTO $playerData): Player
    {
        $team = $this->teamService->getTeamById($playerData->getTeamId());

        try {
            $player = $team->addPlayer(
                $playerData->getFirstName(),
                $playerData->getLastName(),
                $playerData->getAge(),
                $playerData->getPosition()
            );
        } catch (LogicException $e) {
            throw new PlayerLimitExceededException();
        }

        $this->teamService->updateTeamAggregate($team);

        $this->logger->info('[Player] created successfully', $player->toArray());

        return $player;
    }

    /**
     * @throws PlayerNotFoundException
     */
    public function updatePlayer(int $id, PlayerDTO $playerData): Player
    {
        $team = $this->teamService->getTeamByPlayerId($id);
        $player = $team->getPlayerById($id);

        if (!$player) {
            throw new PlayerNotFoundException("Player with id $id not found");
        }

        if ($playerData->getFirstName() !== null || $playerData->getLastName() !== null) {
            $player->rename(
                $playerData->getFirstName() ?? $player->getFirstName(),
                $playerData->getLastName() ?? $player->getLastName()
            );
        }

        if ($playerData->getAge() !== null) {
            $player->changeAge($playerData->getAge());
        }

        if ($playerData->getPosition() !== null) {
            $player->changePosition($playerData->getPosition());
        }

        $this->teamService->updateTeamAggregate($team);

        $this->logger->info('[Player] was updated successfully', $player->toArray());

        return $player;
    }


    /**
     * @throws TeamNotFoundException
     */
    public function getPlayersByTeam(int $id): array
    {
        $team = $this->teamService->getTeamById($id);
        $players = $team->getPlayers();

        return array_map(fn($player) => $player->toArray(), $players->toArray());
    }

    /**
     * @throws PlayerNotFoundException
     */
    public function deletePlayer(int $id): void
    {
        $team = $this->teamService->getTeamByPlayerId($id);
        $player = $team->getPlayerById($id);

        if (!$player) {
            throw new PlayerNotFoundException("Player with id $id not found");
        }

        $this->logger->info("[Player] Deleting player '{$player->getFirstName()} {$player->getLastName()}' with ID: {$id}");

        $team->removePlayer($player);

        $this->teamService->updateTeamAggregate($team);

        $this->logger->info("[Player] Player with ID: {$id} has been deleted successfully");
    }

    /**
     * @throws PlayerNotFoundException
     */
    public function getPlayerById(int $id): Player
    {
        $team = $this->teamService->getTeamByPlayerId($id);
        $player = $team->getPlayerById($id);

        if (!$player) {
            throw new PlayerNotFoundException("Player with id $id not found");
        }

        return $player;
    }

}
