<?php

declare(strict_types=1);

namespace Site\Lead\Domain;

interface LeadRepository
{
    public function findByExternalId(string $externalId): ?Lead;

    public function save(Lead $lead): Lead;
}
