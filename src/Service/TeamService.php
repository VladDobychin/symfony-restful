<?php

namespace App\Service;

use App\Entity\Team;
use Doctrine\ORM\EntityManagerInterface;

class TeamService
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function createTeam(string $name, string $city, int $yearFounded, string $stadiumName): Team
    {
        $team = new Team();
        $team->setName($name)
            ->setCity($city)
            ->setYearFounded($yearFounded)
            ->setStadiumName($stadiumName);

        $this->entityManager->persist($team);
        $this->entityManager->flush();

        return $team;
    }
}