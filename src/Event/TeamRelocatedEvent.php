<?php

namespace App\Event;

class TeamRelocatedEvent
{
    public function __construct(
        public readonly int $teamId,
        public readonly string $oldCity
    ) {}
}
