<?php

declare(strict_types=1);

namespace Site\Lead\Application;

final class AcceptSiteEvent
{
    public function __construct(
        private readonly string $secret,
        private readonly EventIdStore $seen,
        private readonly UpsertLeadByExternalId $upsert,
    ) {
    }

    /**
     * @return array{status: int, body: array<string, mixed>}
     */
    public function handle(string $rawBody, string $signature): array
    {
        $expected = hash_hmac('sha256', $rawBody, $this->secret);
        if ($this->secret === '' || !hash_equals($expected, $signature)) {
            return ['status' => 403, 'body' => ['error' => 'forbidden']];
        }

        $payload = json_decode($rawBody, true);
        if (!is_array($payload)) {
            return ['status' => 400, 'body' => ['error' => 'bad_json']];
        }

        $eventId = (string) ($payload['event_id'] ?? '');
        $externalId = (string) ($payload['external_id'] ?? '');
        if ($eventId === '' || $externalId === '') {
            return ['status' => 400, 'body' => ['error' => 'bad_payload']];
        }

        if ($this->seen->has($eventId)) {
            return ['status' => 202, 'body' => ['accepted' => true, 'duplicate' => true]];
        }

        $this->upsert->handle(
            $externalId,
            (string) ($payload['name'] ?? ''),
            (string) ($payload['phone'] ?? ''),
            (string) ($payload['text'] ?? ''),
        );
        $this->seen->remember($eventId);

        return ['status' => 202, 'body' => ['accepted' => true, 'duplicate' => false]];
    }
}
