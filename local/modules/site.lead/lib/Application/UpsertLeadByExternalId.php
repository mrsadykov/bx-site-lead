<?php

declare(strict_types=1);

namespace Site\Lead\Application;

use Site\Lead\Domain\Lead;
use Site\Lead\Domain\LeadRepository;

final class UpsertLeadByExternalId
{
    public function __construct(
        private readonly LeadRepository $leads,
    ) {
    }

    /**
     * @return array{lead: Lead, created: bool}
     */
    public function handle(string $externalId, string $name, string $phone, string $note): array
    {
        $existing = $this->leads->findByExternalId($externalId);

        if ($existing !== null) {
            $existing->addComment($note);
            $saved = $this->leads->save($existing);

            return ['lead' => $saved, 'created' => false];
        }

        $lead = new Lead($externalId, $name, $phone, [$note]);
        $saved = $this->leads->save($lead);

        return ['lead' => $saved, 'created' => true];
    }
}
