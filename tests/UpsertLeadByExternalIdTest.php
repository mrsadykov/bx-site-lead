<?php

declare(strict_types=1);

namespace Site\Lead\Tests;

use PHPUnit\Framework\TestCase;
use Site\Lead\Application\AcceptSiteEvent;
use Site\Lead\Application\UpsertLeadByExternalId;
use Site\Lead\Infrastructure\CrmLeadRepository;

final class UpsertLeadByExternalIdTest extends TestCase
{
    public function testSameExternalIdAddsCommentToTheSameLead(): void
    {
        $gateway = new MemoryCrmLeadGateway();
        $upsert = new UpsertLeadByExternalId(new CrmLeadRepository($gateway, 'site'));

        $first = $upsert->handle('ext-1', 'Анна', '+79990001122', 'Первый текст');
        $second = $upsert->handle('ext-1', 'Анна', '+79990001122', 'Второй текст');

        self::assertTrue($first['created']);
        self::assertFalse($second['created']);
        self::assertSame($first['lead']->id, $second['lead']->id);
        self::assertSame(['Первый текст', 'Второй текст'], $second['lead']->comments);
        self::assertSame(
            ['crm.lead.list', 'crm.lead.add', 'crm.lead.list', 'crm.lead.update'],
            $gateway->calls(),
        );
    }

    public function testSameEventIdDoesNotAppendTwice(): void
    {
        $gateway = new MemoryCrmLeadGateway();
        $receiver = new AcceptSiteEvent(
            'secret',
            new MemoryEventIdStore(),
            new UpsertLeadByExternalId(new CrmLeadRepository($gateway, 'site')),
        );

        $body = json_encode([
            'event_id' => 'evt-1',
            'external_id' => 'ext-1',
            'name' => 'Анна',
            'phone' => '+79990001122',
            'text' => 'Первый текст',
        ], JSON_UNESCAPED_UNICODE);
        self::assertIsString($body);
        $signature = hash_hmac('sha256', $body, 'secret');

        $first = $receiver->handle($body, $signature);
        $second = $receiver->handle($body, $signature);

        self::assertSame(202, $first['status']);
        self::assertFalse($first['body']['duplicate']);
        self::assertSame(202, $second['status']);
        self::assertTrue($second['body']['duplicate']);
        self::assertSame(['crm.lead.list', 'crm.lead.add'], $gateway->calls());
    }
}
