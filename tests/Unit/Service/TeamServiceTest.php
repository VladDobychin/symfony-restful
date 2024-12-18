<?php

namespace App\Tests\Unit\Service;

use App\Entity\Team;
use App\Event\TeamRelocatedEvent;
use App\Repository\TeamRepository;
use App\Service\TeamService;
use App\Tests\Unit\DTO\TestCreateTeamData;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class TeamServiceTest extends BaseServiceTest
{
    protected TeamService $teamService;
    protected $teamRepository;
    protected $eventDispatcher;

    protected function setUp(): void
    {
        parent::setUp();
        $this->teamRepository = $this->createMock(TeamRepository::class);
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

        $this->expectEntityManager('persist', 'flush', Team::class);
        $this->expectLog('[Team] created successfully');

        $createdTeam = $this->teamService->createTeam($testDto);

        $this->assertInstanceOf(Team::class, $createdTeam);
        $this->assertEquals($testDto->getName(), $createdTeam->getName());
        $this->assertEquals($testDto->getCity(), $createdTeam->getCity());
        $this->assertEquals($testDto->getYearFounded(), $createdTeam->getYearFounded());
        $this->assertEquals($testDto->getStadiumName(), $createdTeam->getStadiumName());
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
        $this->expectLog('[Team] was updated successfully');

        $this->eventDispatcher->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(TeamRelocatedEvent::class));

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
    public function testUpdateTeamWithoutCityChange(): void
    {
        $team = $this->createTeam(1, 'Team A', 'City A', 2000, 'Stadium A');
        $testDto = new TestCreateTeamData('Team B', 'City A', 2001, 'Stadium B');

        $this->expectFindTeamById($team->getId(), $team);

        $this->entityManager->expects($this->once())
            ->method('flush');
        $this->expectLog('[Team] was updated successfully');

        $this->eventDispatcher->expects($this->never())
            ->method('dispatch');

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

    /**
     * @covers \App\Service\TeamService::deleteTeam
     */
    public function testDeleteTeam()
    {
        $team = $this->createTeam(1, 'Team A', 'City A', 2000, 'Stadium A');

        $this->expectFindTeamById($team->getId(), $team);
        $this->teamRepository->expects($this->once())
            ->method('deleteTeam')
            ->with($this->isInstanceOf(Team::class));

        $isDeleted = $this->teamService->deleteTeam($team->getId());

        $this->assertTrue($isDeleted);
    }

    /**
     * @covers \App\Service\TeamService::deleteTeam
     */
    public function testDeleteTeamNotFound(): void
    {
        $this->expectFindTeamById(99, null);

        $this->logger->expects($this->once())
            ->method('warning')
            ->with($this->stringContains('Attempted to delete non-existent team'));

        $isDeleted = $this->teamService->deleteTeam(99);

        $this->assertFalse($isDeleted);
    }
}