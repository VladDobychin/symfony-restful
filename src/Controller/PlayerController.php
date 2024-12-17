<?php

namespace App\Controller;

use App\Request\CreatePlayerRequest;
use App\Service\PlayerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PlayerController extends AbstractController
{
    #[Route('/api/players', name: 'create_player', methods: ['POST'])]
    public function createPlayer(
        CreatePlayerRequest $request,
        PlayerService $playerService
    ): JsonResponse {
        $player = $playerService->createPlayer($request);

        if (!$player) {
            return $this->json(['error' => 'Team not found'], Response::HTTP_BAD_REQUEST);
        }

        return $this->json([
            'id' => $player->getId(),
            'firstName' => $player->getFirstName(),
            'lastName' => $player->getLastName(),
            'age' => $player->getAge(),
            'position' => $player->getPosition(),
            'teamId' => $player->getTeam()->getId(),
        ], Response::HTTP_CREATED);
    }
}
