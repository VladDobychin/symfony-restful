<?php

namespace App\Tests\Unit\Service;

use App\Entity\Team;
use App\Repository\TeamRepository;
use App\Service\TeamService;
use App\Tests\Unit\DTO\TestCreateTeamData;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use ReflectionClass;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class TeamServiceTest extends TestCase
{
    private TeamService $teamService;
    private $entityManager;
    private $teamRepository;
    private $logger;
    private $eventDispatcher;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->teamRepository = $this->createMock(TeamRepository::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $this->teamService = new TeamService(
            $this->entityManager,
            $this->teamRepository,
            $this->logger,
            $this->eventDispatcher
        );
    }

    /**
     * @covers \App\Service\TeamService::createTeam
     */
    public function testCreateTeam(): void
    {
        $testDto = new TestCreateTeamData('Team A', 'City A', 2000, 'Stadium A');

        $this->expectEntityManager();
        $this->expectLog('[Team] created successfully');

        $createdTeam = $this->teamService->createTeam($testDto);

        $this->assertInstanceOf(Team::class, $createdTeam);
        $this->assertEquals('Team A', $createdTeam->getName());
        $this->assertEquals('City A', $createdTeam->getCity());
        $this->assertEquals(2000, $createdTeam->getYearFounded());
        $this->assertEquals('Stadium A', $createdTeam->getStadiumName());
    }

    /**
     * @covers \App\Service\TeamService::getAllTeams
     */
    public function testGetAllTeams(): void
    {
        $team1 = $this->createTeam(1, 'Team A', 'City A', 2000, 'Stadium A');
        $team2 = $this->createTeam(2, 'Team B', 'City B', 2001, 'Stadium B');

        $this->teamRepository->expects($this->once())
            ->method('findAllTeams')
            ->willReturn([$team1, $team2]);

        $teams = $this->teamService->getAllTeams();

        $this->assertCount(2, $teams);
        $this->assertEquals($team1->getId(), $teams[0]->getId());
        $this->assertEquals($team1->getName(), $teams[0]->getName());
        $this->assertEquals($team2->getId(), $teams[1]->getId());
        $this->assertEquals($team2->getName(), $teams[1]->getName());
    }

    /**
     * @covers \App\Service\TeamService::getTeamById
     */
    public function testGetTeamById(): void
    {
        $team = $this->createTeam(1, 'Team A', 'City A', 2000, 'Stadium A');

        $this->expectFindTeamById($team->getId(), $team);

        $retrievedTeam = $this->teamService->getTeamById($team->getId());

        $this->assertEquals($team->getId(), $retrievedTeam->getId());
        $this->assertEquals($team->getName(), $retrievedTeam->getName());
        $this->assertEquals($team->getCity(), $retrievedTeam->getCity());
        $this->assertEquals($team->getYearFounded(), $retrievedTeam->getYearFounded());
        $this->assertEquals($team->getStadiumName(), $retrievedTeam->getStadiumName());
    }

    /**
     * @covers \App\Service\TeamService::getTeamById
     */
    public function testGetTeamByIdNotFound(): void
    {
        $this->expectFindTeamById(99, null);

        $team = $this->teamService->getTeamById(99);

        $this->assertNull($team, 'Assert null when retrieving a team by non-existent id');
    }

    /**
     * @covers \App\Service\TeamService::updateTeam
     */
    public function testUpdateTeam(): void
    {
        $team = $this->createTeam(1, 'Team A', 'City A', 2000, 'Stadium A');
        $testDto = new TestCreateTeamData('Team B', 'City B', 2001, 'Stadium B');

        $this->expectFindTeamById($team->getId(), $team);

        $this->entityManager->expects($this->once())
            ->method('flush');

        $updatedTeam = $this->teamService->updateTeam($team->getId(), $testDto);

        $this->assertEquals($team->getId(), $updatedTeam->getId());
        $this->assertEquals($testDto->getName(), $updatedTeam->getName());
        $this->assertEquals($testDto->getCity(), $updatedTeam->getCity());
        $this->assertEquals($testDto->getStadiumName(), $updatedTeam->getStadiumName());
        $this->assertEquals($testDto->getYearFounded(), $updatedTeam->getYearFounded());
    }

    /**
     * @covers \App\Service\TeamService::updateTeam
     */
    public function testUpdateTeamNotFound(): void
    {
        $testDto = new TestCreateTeamData('Team B', 'City B', 2001, 'Stadium B');

        $this->expectFindTeamById(99, null);

        $updatedTeam = $this->teamService->updateTeam(99, $testDto);

        $this->assertNull($updatedTeam, 'Expected null when updating a non-existent team.');
    }

    private function expectEntityManager(): void
    {
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(Team::class));
        $this->entityManager->expects($this->once())
            ->method('flush');
    }

    private function expectLog(string $message, string $level = 'info'): void
    {
        $this->logger->expects($this->once())
            ->method($level)
            ->with($this->stringContains($message));
    }

    private function createTeam(int $id, string $name, string $city, int $yearFounded, string $stadiumName): Team
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

    private function expectFindTeamById(int $id, mixed $return): void
    {
        $this->teamRepository->expects($this->once())
            ->method('findTeamById')
            ->with($id)
            ->willReturn($return);
    }
}