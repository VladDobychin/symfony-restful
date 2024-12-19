<?php

namespace App\Service;

use App\DTO\PlayerDataInterface;
use App\Entity\Player;
use App\Entity\Team;
use App\Exception\PlayerLimitExceededException;
use App\Exception\PlayerNotFoundException;
use App\Exception\TeamNotFoundException;
use App\Repository\PlayerRepository;
use App\Repository\TeamRepository;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Psr\Log\LoggerInterface;

class PlayerService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private TeamRepository $teamRepository,
        private PlayerRepository $playerRepository,
        private LoggerInterface $logger
    ) {
    }

    public function createPlayer(PlayerDataInterface $request): Player
    {
        $team = $this->teamRepository->findTeamById($request->getTeamId());

        if (!$team) {
            $this->logger->error("[Player] Failed to create player - Team with ID {$request->getTeamId()} not found.");
            throw new TeamNotFoundException("Team with ID {$request->getTeamId()} not found.");
        }

        $player = new Player();
        $player->setFirstName($request->getFirstName())
            ->setLastName($request->getLastName())
            ->setAge($request->getAge())
            ->setPosition($request->getPosition());

        try {
            $team->addPlayer($player);

            $this->entityManager->persist($player);
            $this->entityManager->flush();

            $this->logger->info('[Player] created successfully', [
                'id' => $player->getId(),
                'name' => "{$player->getFirstName()} {$player->getLastName()}",
                'teamId' => $team->getId(),
            ]);

            return $player;
        } catch (LogicException $e) {
            $this->logger->warning("[Player] Failed to add player - {$e->getMessage()}");
            throw new PlayerLimitExceededException();
        }
    }

    public function updatePlayer(int $id, PlayerDataInterface $request): ?Player
    {
        $player = $this->playerRepository->findPlayerById($id);

        if (!$player) {
            return null;
        }

        if ($request->getFirstName() !== null) {
            $player->setFirstName($request->getFirstName());
        }
        if ($request->getLastName() !== null) {
            $player->setLastName($request->getLastName());
        }
        if ($request->getAge() !== null) {
            $player->setAge($request->getAge());
        }
        if ($request->getPosition()) {
            $player->setPosition($request->getPosition());
        }

        $this->logger->info('[Player] was updated successfully', [
            'id' => $player->getId(),
            'name' => "{$player->getFirstName()} {$player->getLastName()}",
        ]);

        $this->entityManager->flush();

        return $player;
    }


    public function getPlayersByTeam(Team $team): array
    {
        return $this->playerRepository->findPlayersByTeam($team);
    }

    public function getPlayerById(int $id): Player
    {
        $player = $this->playerRepository->findPlayerById($id);

        if (!$player) {
            throw new PlayerNotFoundException("Player with id $id not found");
        }

        return $player;
    }

    public function deletePlayer(int $id): bool
    {
        $player = $this->playerRepository->findPlayerById($id);

        if (!$player) {
            $this->logger->warning("[Player] Attempted to delete non-existent player with ID: {$id}");
            return false;
        }

        $this->logger->info("[Player] Deleting player '{$player->getFirstName()} {$player->getLastName()}' with ID: {$id}");

        $this->playerRepository->deletePlayer($player);

        $this->logger->info("[Player] Player with ID: {$id} has been deleted successfully");

        return true;
    }

}
