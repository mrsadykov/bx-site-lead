<?php

declare(strict_types=1);

namespace Site\Lead\Application;

interface EventIdStore
{
    public function has(string $eventId): bool;

    public function remember(string $eventId): void;
}
