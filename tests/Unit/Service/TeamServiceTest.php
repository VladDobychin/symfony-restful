<?php

namespace App\Tests\Unit\Service;

use App\Entity\Team;
use App\Repository\TeamRepository;
use App\Service\TeamService;
use App\Tests\Unit\DTO\TestCreateTeamData;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
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
}
