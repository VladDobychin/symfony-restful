<?php

namespace App\Repository;

use App\Entity\Team;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;


/**
 * @extends ServiceEntityRepository<Team>
 */
class TeamRepository extends ServiceEntityRepository implements TeamRepositoryInterface
{
    public function __construct(
        ManagerRegistry $registry,
        private EventDispatcherInterface $eventDispatcher
    ) {
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

        $this->dispatchDomainEvents($team);
    }

    private function dispatchDomainEvents(Team $team): void
    {
        foreach ($team->popEvents() as $event) {
            $this->eventDispatcher->dispatch($event);
        }
    }
}
