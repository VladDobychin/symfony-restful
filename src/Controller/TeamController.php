<?php

namespace App\Controller;

use App\DTO\TeamDTO;
use App\Exception\TeamNotFoundException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Service\{TeamService, PlayerService};
use Symfony\Component\HttpFoundation\{Request, Response, JsonResponse};
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

class TeamController extends AbstractController
{
    public function __construct(private TeamService $teamService)
    {
    }

    #[Route('/api/teams', name: 'create_team', methods: ['POST'])]
    public function createTeam(
        Request $request,
        ValidatorInterface $validator
    ): JsonResponse {
        $data = $request->toArray();
        $teamData = TeamDTO::fromArray($data);

        $errors = $validator->validate($teamData, null, ['create']);
        if (count($errors) > 0) {
            return $this->handleValidationErrors($errors);
        }

        $team = $this->teamService->createTeam($teamData);

        return $this->json($team->toArray(), Response::HTTP_CREATED);
    }

    #[Route('/api/teams', name: 'get_teams', methods: ['GET'])]
    public function getTeams(): JsonResponse
    {
        return $this->json(
            $this->teamService->getAllTeams()
        );
    }

    #[Route('/api/teams/{id}', name: 'get_team_by_id', methods: ['GET'])]
    public function getTeamById(int $id): JsonResponse
    {
        try {
            $team = $this->teamService->getTeamById($id);

            return $this->json($team->toArray());
        } catch (TeamNotFoundException $exception) {
            return $this->json(['error' => $exception->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('/api/teams/{id}', name: 'update_team', methods: ['PUT'])]
    public function updateTeam(
        int $id,
        Request $request,
        ValidatorInterface $validator
    ): JsonResponse {
        $data = $request->toArray();
        $teamData = TeamDTO::fromArray($data);

        $errors = $validator->validate($teamData, null, ['update']);
        if (count($errors) > 0) {
            return $this->handleValidationErrors($errors);
        }

        try {
            $team = $this->teamService->updateTeam($id, $teamData);

            return $this->json($team->toArray());
        } catch (TeamNotFoundException $exception) {
            return $this->json(['error' => $exception->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('/api/teams/{id}', name: 'delete_team', methods: ['DELETE'])]
    public function deleteTeam(int $id): JsonResponse
    {
        try {
            $this->teamService->deleteTeam($id);

            return $this->json(null, Response::HTTP_NO_CONTENT);
        } catch (TeamNotFoundException $exception) {
            return $this->json(['error' => $exception->getMessage()], Response::HTTP_NOT_FOUND);

        }
    }

    #[Route('/api/teams/{id}/players', name: 'get_players_by_team', methods: ['GET'])]
    public function getPlayersByTeam(
        int $id,
        PlayerService $playerService
    ): JsonResponse {
        try {
            $players = $playerService->getPlayersByTeam($id);

            return $this->json($players);
        } catch (TeamNotFoundException $exception) {
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
