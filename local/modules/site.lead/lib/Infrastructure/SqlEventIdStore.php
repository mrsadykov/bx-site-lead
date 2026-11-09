<?php

declare(strict_types=1);

namespace Site\Lead\Infrastructure;

use Bitrix\Main\Application;
use Bitrix\Main\Type\DateTime;
use Site\Lead\Application\EventIdStore;

final class SqlEventIdStore implements EventIdStore
{
    public function has(string $eventId): bool
    {
        $row = Application::getConnection()->query(
            "SELECT EVENT_ID FROM site_lead_event WHERE EVENT_ID = '" . $this->sql($eventId) . "' LIMIT 1"
        )->fetch();

        return is_array($row);
    }

    public function remember(string $eventId): void
    {
        $receivedAt = (new DateTime())->format('Y-m-d H:i:s');
        Application::getConnection()->queryExecute(
            "INSERT INTO site_lead_event (EVENT_ID, RECEIVED_AT) VALUES ('"
            . $this->sql($eventId) . "', '" . $this->sql($receivedAt) . "')"
        );
    }

    private function sql(string $value): string
    {
        return Application::getConnection()->getSqlHelper()->forSql($value);
    }
}
