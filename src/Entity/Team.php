<?php

namespace App\Entity;

use App\Repository\TeamRepository;
use Doctrine\Common\Collections\{Collection, ArrayCollection};

use Doctrine\ORM\Mapping as ORM;
use LogicException;

#[ORM\Entity(repositoryClass: TeamRepository::class)]
class Team
{
    #[ORM\OneToMany(targetEntity: Player::class, mappedBy: 'team', cascade: ['persist', 'remove'])]
    private Collection $players;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $city = null;

    #[ORM\Column]
    private ?int $yearFounded = null;

    #[ORM\Column(length: 255)]
    private ?string $stadiumName = null;

    public function __construct()
    {
        $this->players = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getYearFounded(): ?int
    {
        return $this->yearFounded;
    }

    public function setYearFounded(int $yearFounded): static
    {
        $this->yearFounded = $yearFounded;

        return $this;
    }

    public function getStadiumName(): ?string
    {
        return $this->stadiumName;
    }

    public function setStadiumName(string $stadiumName): static
    {
        $this->stadiumName = $stadiumName;

        return $this;
    }

    public function getPlayers(): Collection
    {
        return $this->players;
    }

    public function addPlayer(Player $player): static
    {
        if ($this->players->count() >= 11) {
            throw new LogicException('A team cannot have more than 11 players.');
        }

        if (!$this->players->contains($player)) {
            $this->players->add($player);
            $player->setTeam($this);
        }

        return $this;
    }

    public function removePlayer(Player $player): static
    {
        if ($this->players->removeElement($player)) {
            if ($player->getTeam() === $this) {
                $player->setTeam(null);
            }
        }

        return $this;
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
