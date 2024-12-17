<?php

namespace App\EventListener;

use App\Event\TeamRelocatedEvent;
use App\Repository\TeamRepository;
use Psr\Log\LoggerInterface;

class TeamRelocatedListener
{
    public function __construct(
        private LoggerInterface $logger,
        private TeamRepository $teamRepository
    ) {}

    public function onTeamRelocated(TeamRelocatedEvent $event): void
    {
        $team = $this->teamRepository->findTeamById($event->teamId);

        if (!$team) {
            $this->logger->error("Failed to send notifications: Team with ID {$event->teamId} not found.");
            return;
        }

        $oldCity = $event->oldCity;
        $newCity = $team->getCity();

        foreach ($team->getPlayers() as $player) {
            $this->logger->info(sprintf(
                "Notification to Player [%s %s]: Your team [%s] has been relocated from [%s] to [%s].",
                $player->getFirstName(),
                $player->getLastName(),
                $team->getName(),
                $oldCity,
                $newCity
            ));
        }
    }
}
