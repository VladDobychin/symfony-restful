<?php

namespace App\Service;

use App\Entity\Player;
use App\Entity\Team;
use App\Exception\PlayerLimitExceededException;
use App\Repository\PlayerRepository;
use App\Repository\TeamRepository;
use App\Request\CreatePlayerRequest;
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

    public function createPlayer(CreatePlayerRequest $request): ?Player
    {
        $team = $this->teamRepository->findTeamById($request->teamId);

        if (!$team) {
            $this->logger->error("[Player] Failed to create player - Team with ID {$request->teamId} not found.");
            return null;
        }

        $player = new Player();
        $player->setFirstName($request->firstName)
            ->setLastName($request->lastName)
            ->setAge($request->age)
            ->setPosition($request->position);

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

    public function getPlayersByTeam(Team $team): array
    {
        return $this->playerRepository->findPlayersByTeam($team);
    }
}
