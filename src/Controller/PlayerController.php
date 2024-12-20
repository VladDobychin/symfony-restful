<?php

namespace App\Controller;

use App\DTO\PlayerDTO;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use App\Exception\{PlayerLimitExceededException, PlayerNotFoundException, TeamNotFoundException};
use App\Request\{UpdatePlayerRequest};
use App\Service\PlayerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PlayerController extends AbstractController
{
    public function __construct(private PlayerService $playerService)
    {
    }

    #[Route('/api/players', name: 'create_player', methods: ['POST'])]
    public function createPlayer(
        Request $request,
        ValidatorInterface $validator
    ): JsonResponse {
        $data = $request->toArray();
        $playerData = PlayerDTO::fromArray($data);

        $errors = $validator->validate($playerData, null, ['create']);
        if (count($errors) > 0) {
            return $this->handleValidationErrors($errors);
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
        UpdatePlayerRequest $request,
    ): JsonResponse {
        try {
            $player = $this->playerService->updatePlayer($id, $request);

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

    private function handleValidationErrors($violations): JsonResponse
    {
        $errors = [];
        foreach ($violations as $violation) {
            $errors[] = [
                'property' => $violation->getPropertyPath(),
                'value' => $violation->getInvalidValue(),
                'message' => $violation->getMessage(),
            ];
        }

        return $this->json(['errors' => $errors], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
