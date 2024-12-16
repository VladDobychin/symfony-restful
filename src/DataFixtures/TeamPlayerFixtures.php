<?php

namespace App\DataFixtures;

use App\Entity\Player;
use App\Entity\Team;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TeamPlayerFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Create dummy teams
        for ($t = 1; $t <= 3; $t++) {
            $team = new Team();
            $team->setName("Team $t")
                ->setCity("City $t")
                ->setYearFounded(2000 + $t)
                ->setStadiumName("Stadium $t");
            $manager->persist($team);

            // Create dummy players
            for ($p = 1; $p <= 11; $p++) {
                $player = new Player();
                $player->setFirstName("Player $p")
                    ->setLastName("LastName $p")
                    ->setAge(rand(18, 35))
                    ->setPosition("Position $p")
                    ->setTeam($team);
                $manager->persist($player);
            }
        }

        $manager->flush();
    }
}
