<?php

namespace App\Controller;

use App\Exception\TeamNotFoundException;
use App\Request\{CreateTeamRequest, UpdateTeamRequest};
use App\Service\{TeamService, PlayerService};
use Symfony\Component\HttpFoundation\{Response, JsonResponse};
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

class TeamController extends AbstractController
{
    public function __construct(private TeamService $teamService)
    {
    }

    #[Route('/api/teams', name: 'create_team', methods: ['POST'])]
    public function createTeam(
        CreateTeamRequest $request,
    ): JsonResponse {
        $team = $this->teamService->createTeam($request);

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
        UpdateTeamRequest $request
    ): JsonResponse {
        try {
            $team = $this->teamService->updateTeam($id, $request);

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
}
