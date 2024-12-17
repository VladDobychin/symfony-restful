<?php

namespace App\Repository;

use App\Entity\Player;
use App\Entity\Team;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Player>
 */
class PlayerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Player::class);
    }

    public function findPlayersByTeam(Team $team): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.team = :team')
            ->setParameter('team', $team)
            ->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
