<?php

namespace App\Controller;

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

    // TODO: research how to take care of trailing slashes
    #[Route('/api/teams', name: 'create_team', methods: ['POST'])]
    public function createTeam(
        CreateTeamRequest $request,
    ): JsonResponse {
        $team = $this->teamService->createTeam($request);

        return $this->json([
            'id' => $team->getId(),
            'name' => $team->getName(),
            'city' => $team->getCity(),
            'yearFounded' => $team->getYearFounded(),
            'stadiumName' => $team->getStadiumName(),
        ], Response::HTTP_CREATED);
    }

    #[Route('/api/teams', name: 'get_teams', methods: ['GET'])]
    public function getTeams(): JsonResponse
    {
        $teams = $this->teamService->getAllTeams();

        $teamData = array_map(fn($team) => [
            'id' => $team->getId(),
            'name' => $team->getName(),
            'city' => $team->getCity(),
            'yearFounded' => $team->getYearFounded(),
            'stadiumName' => $team->getStadiumName(),
        ], $teams);

        return $this->json($teamData);
    }

    #[Route('/api/teams/{id}', name: 'get_team_by_id', methods: ['GET'])]
    public function getTeamById(int $id): JsonResponse
    {
        $team = $this->teamService->getTeamById($id);

        if (!$team) {
            return $this->json(['error' => 'Team not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'id' => $team->getId(),
            'name' => $team->getName(),
            'city' => $team->getCity(),
            'yearFounded' => $team->getYearFounded(),
            'stadiumName' => $team->getStadiumName(),
        ]);
    }

    #[Route('/api/teams/{id}', name: 'update_team', methods: ['PUT'])]
    public function updateTeam(
        int $id,
        UpdateTeamRequest $request
    ): JsonResponse {
        $team = $this->teamService->updateTeam($id, $request);

        if (!$team) {
            return $this->json(['error' => 'Team not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'id' => $team->getId(),
            'name' => $team->getName(),
            'city' => $team->getCity(),
            'yearFounded' => $team->getYearFounded(),
            'stadiumName' => $team->getStadiumName(),
        ]);
    }

    #[Route('/api/teams/{id}', name: 'delete_team', methods: ['DELETE'])]
    public function deleteTeam(int $id): JsonResponse
    {
        $isDeleted = $this->teamService->deleteTeam($id);

        if (!$isDeleted) {
            return $this->json(['error' => 'Team not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/teams/{id}/players', name: 'get_players_by_team', methods: ['GET'])]
    public function getPlayersByTeam(
        int $id,
        PlayerService $playerService
    ): JsonResponse {
        $team = $this->teamService->getTeamById($id);

        if (!$team) {
            return $this->json(['error' => 'Team not found'], Response::HTTP_NOT_FOUND);
        }

        $players = $playerService->getPlayersByTeam($team);

        $playerData = array_map(fn($player) => [
            'id' => $player->getId(),
            'firstName' => $player->getFirstName(),
            'lastName' => $player->getLastName(),
            'age' => $player->getAge(),
            'position' => $player->getPosition(),
        ], $players);

        return $this->json($playerData);
    }
}
