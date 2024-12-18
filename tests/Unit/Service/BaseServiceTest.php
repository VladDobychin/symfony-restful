<?php

namespace App\Tests\Unit\Service;

use App\Entity\Team;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use ReflectionClass;

abstract class BaseServiceTest extends TestCase
{
    protected $entityManager;
    protected $logger;

    protected function setUp(): void
    {
        parent::setUp();
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
    }

    protected function createTeam(int $id, string $name, string $city, int $yearFounded, string $stadiumName): Team
    {
        $team = new Team();
        $team->setName($name)
            ->setCity($city)
            ->setYearFounded($yearFounded)
            ->setStadiumName($stadiumName);

        $reflection = new ReflectionClass(Team::class);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setValue($team, $id);

        return $team;
    }

    protected function expectLog(string $message, string $level = 'info'): void
    {
        $this->logger->expects($this->once())
            ->method($level)
            ->with($this->stringContains($message));
    }

    protected function expectEntityManager(string $method1, string $method2, string $class): void
    {
        $this->entityManager->expects($this->once())
            ->method($method1)
            ->with($this->isInstanceOf($class));
        $this->entityManager->expects($this->once())
            ->method($method2);
    }

    protected function expectFindTeamById(int $id, mixed $return): void
    {
        $this->teamRepository->expects($this->once())
            ->method('findTeamById')
            ->with($id)
            ->willReturn($return);
    }
}
