<?php

namespace App\DTO;

interface CreateTeamDataInterface
{
    public function getName(): string;
    public function getCity(): string;
    public function getYearFounded(): int;
    public function getStadiumName(): string;
}
