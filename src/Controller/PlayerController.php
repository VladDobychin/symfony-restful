<?php

namespace App\Controller;

use App\Exception\PlayerLimitExceededException;
use App\Exception\TeamNotFoundException;
use App\Request\{CreatePlayerRequest, UpdatePlayerRequest};
use App\Service\PlayerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PlayerController extends AbstractController
{
    public function __construct(private PlayerService $playerService)
    {
    }

    #[Route('/api/players', name: 'create_player', methods: ['POST'])]
    public function createPlayer(
        CreatePlayerRequest $request,
    ): JsonResponse {
        try {
            $player = $this->playerService->createPlayer($request);

            return $this->json($player->toArray(), Response::HTTP_CREATED);
        } catch (TeamNotFoundException $exception) {
            return $this->json(['error' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (PlayerLimitExceededException $exception) {
            return $this->json(['error' => $exception->getMessage()], Response::HTTP_CONFLICT);
        }
    }

    #[Route('/api/players/{id}', name: 'get_player_by_id', methods: ['GET'])]
    public function getPlayerById(int $id): JsonResponse
    {
        $player = $this->playerService->getPlayerById($id);

        if (!$player) {
            return $this->json(['error' => 'Player not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'id' => $player->getId(),
            'firstName' => $player->getFirstName(),
            'lastName' => $player->getLastName(),
            'age' => $player->getAge(),
            'position' => $player->getPosition(),
            'teamId' => $player->getTeam()->getId(),
        ]);
    }

    #[Route('/api/players/{id}', name: 'update_player', methods: ['PUT'])]
    public function updatePlayer(
        int $id,
        UpdatePlayerRequest $request,
    ): JsonResponse {
        $player = $this->playerService->updatePlayer($id, $request);

        if (!$player) {
            return $this->json(['error' => 'Player not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'id' => $player->getId(),
            'firstName' => $player->getFirstName(),
            'lastName' => $player->getLastName(),
            'age' => $player->getAge(),
            'position' => $player->getPosition(),
            'teamId' => $player->getTeam()->getId(),
        ]);
    }

    #[Route('/api/players/{id}', name: 'delete_player', methods: ['DELETE'])]
    public function deletePlayer(int $id): JsonResponse
    {
        $isDeleted = $this->playerService->deletePlayer($id);

        if (!$isDeleted) {
            return $this->json(['error' => 'Player not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

}
