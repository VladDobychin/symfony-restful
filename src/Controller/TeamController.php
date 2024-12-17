<?php

namespace App\Controller;

use App\Request\CreateTeamRequest;
use App\Service\TeamService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TeamController extends AbstractController
{
    #[Route('/api/teams', name: 'create_team', methods: ['POST'])]
    public function createTeam(
        CreateTeamRequest $request,
        TeamService $teamService
    ): JsonResponse {
        $team = $teamService->createTeam(
            $request->name,
            $request->city,
            $request->yearFounded,
            $request->stadiumName
        );

        return $this->json([
            'id' => $team->getId(),
            'name' => $team->getName(),
            'city' => $team->getCity(),
            'yearFounded' => $team->getYearFounded(),
            'stadiumName' => $team->getStadiumName(),
        ], Response::HTTP_CREATED);
    }

    #[Route('/api/teams', name: 'get_teams', methods: ['GET'])]
    public function getTeams(TeamService $teamService): JsonResponse
    {
        $teams = $teamService->getAllTeams();

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
    public function getTeamById(int $id, TeamService $teamService): JsonResponse
    {
        $team = $teamService->getTeamById($id);

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
}
