<?php

namespace App\Repository;

use App\Entity\Team;

interface TeamRepositoryInterface
{
    public function findTeamById(int $id): ?Team;
    public function findAllTeams(): array;
    public function findTeamByPlayerId(int $playerId): ?Team;
    public function saveTeam(Team $team): void;
    public function deleteTeam(Team $team): void;
}
