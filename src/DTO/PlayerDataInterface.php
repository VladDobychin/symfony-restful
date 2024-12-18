<?php

namespace App\DTO;
interface PlayerDataInterface
{
    public function getFirstName(): ?string;
    public function getLastName(): ?string;
    public function getAge(): ?int;
    public function getPosition(): ?string;
    public function getTeamId(): ?int;
}