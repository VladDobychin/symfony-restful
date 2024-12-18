<?php

namespace App\Tests\Unit\Service;

use App\Entity\Player;
use App\Entity\Team;
use App\Exception\PlayerLimitExceededException;
use App\Repository\PlayerRepository;
use App\Repository\TeamRepository;
use App\Service\PlayerService;
use App\Tests\Unit\DTO\TestCreatePlayerData;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use ReflectionClass;

class PlayerServiceTest extends TestCase
{
    private PlayerService $playerService;
    private $entityManager;
    private $playerRepository;
    private $teamRepository;
    private $logger;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->playerRepository = $this->createMock(PlayerRepository::class);
        $this->teamRepository = $this->createMock(TeamRepository::class);
        $this->logger = $this->createMock(LoggerInterface::class);

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

    /**
     * @covers \App\Service\PlayerService::createPlayer
     */
    public function testCreatePlayer(): void
    {
        $team = $this->createTeam(1, 'Team A', 'City A', 2000, 'Stadium A');
        $playerData = new TestCreatePlayerData('John', 'Doe', 25, 'Forward', 1);

        $this->expectFindTeamById($playerData->getTeamId(), $team);
        $this->expectEntityManager('persist', 'flush', Player::class);
        $this->expectLog('[Player] created successfully');

        $player = $this->playerService->createPlayer($playerData);

        $this->assertInstanceOf(Player::class, $player);
        $this->assertEquals('John', $player->getFirstName());
        $this->assertEquals('Doe', $player->getLastName());
        $this->assertEquals(25, $player->getAge());
        $this->assertEquals('Forward', $player->getPosition());
        $this->assertSame($team, $player->getTeam());
    }


    /**
     * @covers \App\Service\PlayerService::createPlayer
     */
    public function testCreatePlayerTeamNotFound(): void
    {
        $playerData = new TestCreatePlayerData('John', 'Doe', 25, 'Forward', 99);

        $this->expectFindTeamById($playerData->getTeamId(), null);
        $this->expectLog("Failed to create player - Team with ID {$playerData->getTeamId()} not found.", 'error');

        $player = $this->playerService->createPlayer($playerData);

        $this->assertNull($player);
    }

    /**
     * @covers \App\Service\PlayerService::createPlayer
     */
    public function testCreatePlayerExceedsLimit(): void
    {
        $team = $this->createTeam(1, 'Team A', 'City A', 2000, 'Stadium A');
        $playerData = new TestCreatePlayerData('John', 'Doe', 25, 'Forward', 1);

        $this->expectFindTeamById($playerData->getTeamId(), $team);

        $team->addPlayer($this->createPlayer(1, 'Jane', 'Smith', 30, 'Goalkeeper', $team));
        for ($i = 2; $i <= 11; $i++) {
            $team->addPlayer($this->createPlayer($i, "Player $i", 'Test', 22, 'Defender', $team));
        }

        $this->expectException(PlayerLimitExceededException::class);

        $this->playerService->createPlayer($playerData);
    }

    /**
     * @covers \App\Service\PlayerService::getPlayerById
     */
    public function testGetPlayerByIdSuccessfully(): void
    {
        $team = $this->createTeam(1, 'Team A', 'City A', 2000, 'Stadium A');
        $player = $this->createPlayer(1, 'John', 'Doe', 25, 'Forward', $team);

        $this->expectFindPlayerById($player->getId(), $player);

        $result = $this->playerService->getPlayerById($player->getId());

        $this->assertInstanceOf(Player::class, $result);
        $this->assertEquals($player->getId(), $result->getId());
        $this->assertEquals($player->getFirstName(), $result->getFirstName());
        $this->assertEquals($player->getLastName(), $result->getLastName());
        $this->assertSame($team, $result->getTeam());
    }

    /**
     * @covers \App\Service\PlayerService::getPlayerById
     */
    public function testGetPlayerByIdNotFound(): void
    {
        $playerId = 99;

        $this->expectFindPlayerById($playerId, null);
        $result = $this->playerService->getPlayerById($playerId);

        $this->assertNull($result);
    }

    /**
     * @covers \App\Service\PlayerService::updatePlayer
     */
    public function testUpdatePlayer(): void
    {
        $team = $this->createTeam(1, 'Team A', 'City A', 2000, 'Stadium A');
        $player = $this->createPlayer(1, 'John', 'Doe', 25, 'Forward', $team);

        $updatedData = new TestCreatePlayerData('Jane', 'Smith', 30, 'Midfielder', 1);

        $this->expectFindPlayerById($player->getId(), $player);

        $this->expectLog('[Player] was updated successfully');

        $this->entityManager->expects($this->once())
            ->method('flush');

        $updatedPlayer = $this->playerService->updatePlayer($player->getId(), $updatedData);

        $this->assertInstanceOf(Player::class, $updatedPlayer);
        $this->assertEquals($updatedData->getFirstName(), $updatedPlayer->getFirstName());
        $this->assertEquals($updatedData->getLastName(), $updatedPlayer->getLastName());
        $this->assertEquals($updatedData->getAge(), $updatedPlayer->getAge());
        $this->assertEquals($updatedData->getPosition(), $updatedPlayer->getPosition());
    }

    /**
     * @covers \App\Service\PlayerService::updatePlayer
     */
    public function testUpdatePlayerNotFound(): void
    {
        $playerId = 99;
        $updatedData = new TestCreatePlayerData('Jane', 'Smith', 30, 'Midfielder', 1);

        $this->expectFindPlayerById($playerId, null);

        $this->logger->expects($this->never())->method('info');
        $this->entityManager->expects($this->never())->method('flush');

        $updatedPlayer = $this->playerService->updatePlayer($playerId, $updatedData);

        $this->assertNull($updatedPlayer);
    }

    /**
     * @covers \App\Service\PlayerService::deletePlayer
     */
    public function testDeletePlayer(): void
    {
        $team = $this->createTeam(1, 'Team A', 'City A', 2000, 'Stadium A');
        $player = $this->createPlayer(1, 'John', 'Doe', 25, 'Forward', $team);

        $this->expectFindPlayerById($player->getId(), $player);

        $this->playerRepository->expects($this->once())
            ->method('deletePlayer')
            ->with($player);

        $result = $this->playerService->deletePlayer($player->getId());

        $this->assertTrue($result);
    }

    /**
     * @covers \App\Service\PlayerService::deletePlayer
     */
    public function testDeletePlayerNotFound(): void
    {
        $playerId = 99;

        $this->expectFindPlayerById($playerId, null);

        $this->playerRepository->expects($this->never())
            ->method('deletePlayer');

        $this->expectLog("Attempted to delete non-existent player with ID: {$playerId}", 'warning');

        $result = $this->playerService->deletePlayer($playerId);

        $this->assertFalse($result);
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
    private function expectEntityManager($method1, $method2, $class): void
    {
        $this->entityManager->expects($this->once())
            ->method($method1)
            ->with($this->isInstanceOf($class));
        $this->entityManager->expects($this->once())
            ->method($method2);
    }

    // Duplicate
    private function expectFindTeamById(int $id, mixed $return): void
    {
        $this->teamRepository->expects($this->once())
            ->method('findTeamById')
            ->with($id)
            ->willReturn($return);
    }

    private function expectFindPlayerById(int $id, mixed $return): void
    {
        $this->playerRepository->expects($this->once())
            ->method('findPlayerById')
            ->with($id)
            ->willReturn($return);
    }
}