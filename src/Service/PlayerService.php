<?php

namespace App\Service;

use App\DTO\PlayerDataInterface;
use App\Entity\Player;
use App\Entity\Team;
use App\Exception\PlayerLimitExceededException;
use App\Repository\PlayerRepository;
use App\Repository\TeamRepository;
use App\Request\{CreatePlayerRequest, UpdatePlayerRequest};
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

    public function createPlayer(PlayerDataInterface $request): ?Player
    {
        $team = $this->teamRepository->findTeamById($request->getTeamId());

        if (!$team) {
            $this->logger->error("[Player] Failed to create player - Team with ID {$request->getTeamId()} not found.");
            return null;
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

    public function updatePlayer(int $id, UpdatePlayerRequest $request): ?Player
    {
        $player = $this->playerRepository->findPlayerById($id);

        if (!$player) {
            return null;
        }

        if (isset($request->firstName)) {
            $player->setFirstName($request->firstName);
        }
        if (isset($request->lastName)) {
            $player->setLastName($request->lastName);
        }
        if (isset($request->age)) {
            $player->setAge($request->age);
        }
        if (isset($request->position)) {
            $player->setPosition($request->position);
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

    public function getPlayerById(int $id): ?Player
    {
        return $this->playerRepository->findPlayerById($id);
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
