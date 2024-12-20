<?php

namespace App\Entity;

use App\Repository\TeamRepository;
use Doctrine\Common\Collections\{Collection, ArrayCollection};

use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use LogicException;

#[ORM\Entity(repositoryClass: TeamRepository::class)]
class Team
{
    #[ORM\OneToMany(
        targetEntity:
        Player::class,
        mappedBy: 'team',
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    private Collection $players;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column(length: 255)]
    private string $city;

    #[ORM\Column]
    private int $yearFounded;

    #[ORM\Column(length: 255)]
    private string $stadiumName;

    public function __construct(
        string $name,
        string $city,
        int $yearFounded,
        string $stadiumName
    ) {
        if (empty($name) || empty($city) || empty($stadiumName)) {
            throw new InvalidArgumentException('Name, city and stadium cannot be empty.');
        }

        if ($yearFounded < 1850 || $yearFounded > (int)date('Y')) {
            throw new InvalidArgumentException('Invalid year founded.');
        }

        $this->name = $name;
        $this->city = $city;
        $this->yearFounded = $yearFounded;
        $this->stadiumName = $stadiumName;
        $this->players = new ArrayCollection();
    }

    public function relocateTeam(string $newCity): void
    {
        if (empty($newCity)) {
            throw new InvalidArgumentException('City cannot be empty.');
        }

        $this->city = $newCity;
    }

    public function changeStadium(string $newStadium): void
    {
        if (empty($newStadium)) {
            throw new InvalidArgumentException('Stadium name cannot be empty.');
        }

        $this->stadiumName = $newStadium;
    }

    public function changeYearFounded(int $yearFounded): void
    {
        if (empty($yearFounded)) {
            throw new InvalidArgumentException('Year founded cannot be empty.');
        }

        $this->yearFounded = $yearFounded;
    }

    public function renameTeam(string $newName): void
    {
        if (empty($newName)) {
            throw new InvalidArgumentException('Name cannot be empty.');
        }

        $this->name = $newName;
    }

    public function addPlayer(string $firstName, string $lastName, int $age, string $position): Player
    {
        if ($this->players->count() >= 11) {
            throw new LogicException('A team cannot have more than 11 players.');
        }

        $player = new Player($this, $firstName, $lastName, $age, $position);
        $this->players->add($player);

        return $player;
    }

    public function removePlayer(Player $player): void
    {
        if ($this->players->removeElement($player)) {
            if ($player->getTeam() === $this) {
                $player->leaveTeam();
            }
        }
    }

    public function getPlayerById(int $playerId): ?Player
    {
        foreach ($this->players as $player) {
            if ($player->getId() === $playerId) {
                return $player;
            }
        }
        return null;
    }

    public function getPlayers(): Collection
    {
        return $this->players;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getYearFounded(): int
    {
        return $this->yearFounded;
    }

    public function getStadiumName(): string
    {
        return $this->stadiumName;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'city' => $this->getCity(),
            'yearFounded' => $this->getYearFounded(),
            'stadiumName' => $this->getStadiumName()
        ];
    }
}
