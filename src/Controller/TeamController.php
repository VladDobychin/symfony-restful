<?php

namespace App\Controller;

use App\DTO\TeamDTO;
use App\Exception\TeamNotFoundException;
use App\Service\{DTOValidationService, TeamService, PlayerService};
use Symfony\Component\HttpFoundation\{Request, Response, JsonResponse};
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

class TeamController extends AbstractController
{
    public function __construct(private TeamService $teamService, private DTOValidationService $dtoValidationService)
    {
    }

    #[Route('/api/teams', name: 'create_team', methods: ['POST'])]
    public function createTeam(
        Request $request,
    ): JsonResponse {
        $teamData = TeamDTO::fromArray($request->toArray());

        $errors = $this->dtoValidationService->validate($teamData, ['create']);
        if (!empty($errors)) {
            return $this->json(['errors' => $errors], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $teamDto = $this->teamService->createTeam($teamData);

        return $this->json($teamDto->toArray(), Response::HTTP_CREATED);
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
            $teamDto = $this->teamService->getTeamByIdDTO($id);

            return $this->json($teamDto->toArray());
        } catch (TeamNotFoundException $exception) {
            return $this->json(['error' => $exception->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('/api/teams/{id}', name: 'update_team', methods: ['PUT'])]
    public function updateTeam(
        int $id,
        Request $request,
    ): JsonResponse {
        $teamData = TeamDTO::fromArray($request->toArray());

        $errors = $this->dtoValidationService->validate($teamData, ['update']);
        if (!empty($errors)) {
            return $this->json(['errors' => $errors], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $teamDto = $this->teamService->updateTeam($id, $teamData);

            return $this->json($teamDto->toArray());
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

}
