<?php

namespace App\DTO;

interface TeamDataInterface
{
    public function getName(): ?string;
    public function getCity(): ?string;
    public function getYearFounded(): ?int;
    public function getStadiumName(): ?string;
}
