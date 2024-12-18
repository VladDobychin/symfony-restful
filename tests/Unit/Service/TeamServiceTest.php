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

        $team = new Team();
        $team->setName('Team A')
            ->setCity('City A')
            ->setYearFounded(2000)
            ->setStadiumName('Stadium A');

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(Team::class));
        $this->entityManager->expects($this->once())
            ->method('flush');

        $this->logger->expects($this->once())
            ->method('info')
            ->with($this->stringContains('[Team] created successfully'));

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
        $team1 = new Team();
        $team1->setName('Team A')
            ->setCity('City A')
            ->setYearFounded(2000)
            ->setStadiumName('Stadium A');

        $team2 = new Team();
        $team2->setName('Team B')
            ->setCity('City B')
            ->setYearFounded(2005)
            ->setStadiumName('Stadium B');

        $reflection = new ReflectionClass(Team::class);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setValue($team1, 1);
        $idProperty->setValue($team2, 2);

        $this->teamRepository->expects($this->once())
            ->method('findAllTeams')
            ->willReturn([$team1, $team2]);

        $teams = $this->teamService->getAllTeams();

        $this->assertCount(2, $teams);
        $this->assertEquals(1, $teams[0]->getId());
        $this->assertEquals('Team A', $teams[0]->getName());
        $this->assertEquals(2, $teams[1]->getId());
        $this->assertEquals('Team B', $teams[1]->getName());
    }

    /**
     * @covers \App\Service\TeamService::getTeamById
     */
    public function testGetTeamById(): void
    {
        $team = new Team();
        $team->setName('Team A')
            ->setCity('City A')
            ->setYearFounded(2000)
            ->setStadiumName('Stadium A');

        $reflection = new ReflectionClass(Team::class);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setValue($team, 1);

        $this->teamRepository->expects($this->once())
            ->method('findTeamById')
            ->with($team->getId())
            ->willReturn($team);

        $team = $this->teamService->getTeamById($team->getId());

        $this->assertEquals(1, $team->getId());
        $this->assertEquals('Team A', $team->getName());
        $this->assertEquals('City A', $team->getCity());
        $this->assertEquals(2000, $team->getYearFounded());
        $this->assertEquals('Stadium A', $team->getStadiumName());
    }

    /**
     * @covers \App\Service\TeamService::getTeamById
     */
    public function testGetTeamByIdNotFound(): void
    {
        $this->teamRepository->expects($this->once())
            ->method('findTeamById')
            ->with(99)
            ->willReturn(null);

        $team = $this->teamService->getTeamById(99);

        $this->assertNull($team);
    }

}
