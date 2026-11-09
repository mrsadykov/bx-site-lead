<?php

declare(strict_types=1);

namespace Site\Lead\Domain;

final class Lead
{
    /** @param list<string> $comments */
    public function __construct(
        public readonly string $externalId,
        public string $name,
        public string $phone,
        public array $comments = [],
        public ?int $id = null,
    ) {
    }

    public function addComment(string $text): void
    {
        $this->comments[] = $text;
    }
}
