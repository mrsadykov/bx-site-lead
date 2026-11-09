<?php

declare(strict_types=1);

namespace Site\Lead\Tests;

use Site\Lead\Infrastructure\CrmLeadGateway;

final class MemoryCrmLeadGateway implements CrmLeadGateway
{
    /** @var array<int, array<string, mixed>> */
    private array $rows = [];

    private int $autoIncrement = 1;

    /** @var list<string> */
    private array $calls = [];

    public function call(string $method, array $params): mixed
    {
        $this->calls[] = $method;

        return match ($method) {
            'crm.lead.list' => $this->list($params),
            'crm.lead.add' => $this->add($params),
            'crm.lead.update' => $this->update($params),
            default => throw new \InvalidArgumentException('Неизвестный метод CRM: ' . $method),
        };
    }

    /**
     * @return list<string>
     */
    public function calls(): array
    {
        return $this->calls;
    }

    /**
     * @param array<string, mixed> $params
     * @return list<array<string, mixed>>
     */
    private function list(array $params): array
    {
        $filter = is_array($params['filter'] ?? null) ? $params['filter'] : [];
        $found = [];

        foreach ($this->rows as $row) {
            if (($row['ORIGINATOR_ID'] ?? null) !== ($filter['ORIGINATOR_ID'] ?? null)) {
                continue;
            }
            if (($row['ORIGIN_ID'] ?? null) !== ($filter['ORIGIN_ID'] ?? null)) {
                continue;
            }
            $found[] = $row;
        }

        return $found;
    }

    /**
     * @param array<string, mixed> $params
     */
    private function add(array $params): int
    {
        $fields = is_array($params['fields'] ?? null) ? $params['fields'] : [];
        $id = $this->autoIncrement++;
        $fields['ID'] = (string) $id;
        $this->rows[$id] = $fields;

        return $id;
    }

    /**
     * @param array<string, mixed> $params
     */
    private function update(array $params): bool
    {
        $id = (int) ($params['id'] ?? 0);
        if (!isset($this->rows[$id])) {
            throw new \RuntimeException('Лид ' . $id . ' в CRM не найден');
        }

        $fields = is_array($params['fields'] ?? null) ? $params['fields'] : [];
        $this->rows[$id] = array_merge($this->rows[$id], $fields, ['ID' => (string) $id]);

        return true;
    }
}
