<?php

namespace App\Tests\Unit\Service;

use App\Entity\Player;
use App\Entity\Team;
use App\Repository\PlayerRepository;
use App\Repository\TeamRepository;
use App\Service\PlayerService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use ReflectionClass;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class PlayerServiceTest extends TestCase
{
    private PlayerService $playerService;
    private $entityManager;
    private $playerRepository;
    private $teamRepository;
    private $logger;
    private $eventDispatcher;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->playerRepository = $this->createMock(PlayerRepository::class);
        $this->teamRepository = $this->createMock(TeamRepository::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $this->playerService = new PlayerService(
            $this->entityManager,
            $this->teamRepository,
            $this->playerRepository,
            $this->logger
        );
    }

    /**
     * @covers \App\Service\PlayerService::getPlayersByTeam
     */
    public function testGetPlayersByTeam(): void
    {
        $team = $this->createTeam(1, 'Team A', 'City A', 2000, 'Stadium A');
        $player1 = $this->createPlayer(1, 'John', 'Doe', 25, 'Forward', $team);
        $player2 = $this->createPlayer(2, 'Jane', 'Smith', 22, 'Midfielder', $team);

        $this->playerRepository->expects($this->once())
            ->method('findPlayersByTeam')
            ->with($team)
            ->willReturn([$player1, $player2]);

        $players = $this->playerService->getPlayersByTeam($team);

        $this->assertCount(2, $players);
        $this->assertEquals($player1->getId(), $players[0]->getId());
        $this->assertEquals($player1->getFirstName(), $players[0]->getFirstName());
        $this->assertEquals($player2->getId(), $players[1]->getId());
        $this->assertEquals($player2->getFirstName(), $players[1]->getFirstName());
    }

    /**
     * @covers \App\Service\PlayerService::getPlayersByTeam
     */
    public function testGetPlayersByTeamNoPlayers(): void
    {
        $team = $this->createTeam(1, 'Team A', 'City A', 2000, 'Stadium A');

        $this->playerRepository->expects($this->once())
            ->method('findPlayersByTeam')
            ->with($team)
            ->willReturn([]);

        $players = $this->playerService->getPlayersByTeam($team);

        $this->assertCount(0, $players);
    }

    private function createPlayer(
        int $id,
        string $firstName,
        string $lastName,
        int $age,
        string $position,
        Team $team
    ): Player {
        $player = new Player();
        $player->setFirstName($firstName)
            ->setLastName($lastName)
            ->setAge($age)
            ->setPosition($position)
            ->setTeam($team);

        $reflection = new ReflectionClass(Player::class);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setValue($player, $id);

        return $player;
    }

    // Duplicate
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

    // Duplicate
    private function expectLog(string $message, string $level = 'info'): void
    {
        $this->logger->expects($this->once())
            ->method($level)
            ->with($this->stringContains($message));
    }

    // Duplicate
    private function expectEntityManager(): void
    {
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(Team::class));
        $this->entityManager->expects($this->once())
            ->method('flush');
    }

}