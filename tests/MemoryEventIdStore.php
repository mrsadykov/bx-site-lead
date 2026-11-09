<?php

declare(strict_types=1);

namespace Site\Lead\Tests;

use Site\Lead\Application\EventIdStore;

final class MemoryEventIdStore implements EventIdStore
{
    /** @var array<string, true> */
    private array $ids = [];

    public function has(string $eventId): bool
    {
        return isset($this->ids[$eventId]);
    }

    public function remember(string $eventId): void
    {
        $this->ids[$eventId] = true;
    }
}
