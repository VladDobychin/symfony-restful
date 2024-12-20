<?php

namespace App\Repository;

use App\Entity\Team;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Team>
 */
class TeamRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Team::class);
    }

    public function findAllTeams(): array
    {
        return $this->createQueryBuilder('t')
            ->orderBy('t.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findTeamById(int $id): ?Team
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findTeamByPlayerId(int $playerId): ?Team
    {
        return $this->createQueryBuilder('t')
            ->join('t.players', 'p')
            ->andWhere('p.id = :playerId')
            ->setParameter('playerId', $playerId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function deleteTeam(Team $team): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($team);
        $entityManager->flush();
    }

    public function saveTeam(Team $team): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($team);
        $entityManager->flush();
    }
}
