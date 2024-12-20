<?php

namespace App\Controller;

use App\DTO\PlayerDTO;
use App\Service\DTOValidationService;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use App\Exception\{PlayerLimitExceededException, PlayerNotFoundException, TeamNotFoundException};
use App\Service\PlayerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PlayerController extends AbstractController
{
    public function __construct(
        private PlayerService $playerService,
        private DTOValidationService $dtoValidationService
    ) {
    }

    #[Route('/api/players', name: 'create_player', methods: ['POST'])]
    public function createPlayer(
        Request $request,
    ): JsonResponse {
        $playerData = PlayerDTO::fromArray($request->toArray());

        $errors = $this->dtoValidationService->validate($playerData, ['create']);
        if (!empty($errors)) {
            return $this->json(['errors' => $errors], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $player = $this->playerService->createPlayer($playerData);

            return $this->json($player->toArray(), Response::HTTP_CREATED);
        } catch (TeamNotFoundException $exception) {
            return $this->json(['error' => $exception->getMessage()], Response::HTTP_NOT_FOUND);
        } catch (PlayerLimitExceededException $exception) {
            return $this->json(['error' => $exception->getMessage()], Response::HTTP_CONFLICT);
        }
    }

    #[Route('/api/players/{id}', name: 'get_player_by_id', methods: ['GET'])]
    public function getPlayerById(int $id): JsonResponse
    {
        try {
            $player = $this->playerService->getPlayerById($id);

            return $this->json($player->toArray());
        } catch (PlayerNotFoundException $exception) {
            return $this->json(['error' => $exception->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('/api/players/{id}', name: 'update_player', methods: ['PUT'])]
    public function updatePlayer(
        int $id,
        Request $request,
    ): JsonResponse {
        $playerData = PlayerDTO::fromArray($request->toArray());

        $errors = $this->dtoValidationService->validate($playerData, ['update']);
        if (!empty($errors)) {
            return $this->json(['errors' => $errors], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $player = $this->playerService->updatePlayer($id, $playerData);

            return $this->json($player->toArray());
        } catch (PlayerNotFoundException $exception) {
            return $this->json(['error' => $exception->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('/api/players/{id}', name: 'delete_player', methods: ['DELETE'])]
    public function deletePlayer(int $id): JsonResponse
    {
        try {
            $this->playerService->deletePlayer($id);

            return $this->json(null, Response::HTTP_NO_CONTENT);
        } catch (PlayerNotFoundException $exception) {
            return $this->json(['error' => $exception->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }

}
