<?php

declare(strict_types=1);

namespace Site\Lead\Infrastructure;

final class PortalCrmLeadGateway implements CrmLeadGateway
{
    public function call(string $method, array $params): mixed
    {
        if (!class_exists(\CCrmLead::class)) {
            throw new \RuntimeException('Модуль crm не подключён.');
        }

        $lead = new \CCrmLead(false);

        return match ($method) {
            'crm.lead.list' => $this->list($params),
            'crm.lead.add' => (int) $lead->Add($this->toCrmFields($params['fields'] ?? [])),
            'crm.lead.update' => $lead->Update((int) ($params['id'] ?? 0), $this->toCrmFields($params['fields'] ?? [])),
            default => throw new \InvalidArgumentException('Неизвестный метод CRM: ' . $method),
        };
    }

    /**
     * @param array<string, mixed> $params
     * @return list<array<string, mixed>>
     */
    private function list(array $params): array
    {
        $rows = [];
        $result = \CCrmLead::GetListEx(
            ['ID' => 'ASC'],
            is_array($params['filter'] ?? null) ? $params['filter'] : [],
            false,
            false,
            is_array($params['select'] ?? null) ? $params['select'] : ['ID'],
        );

        while ($row = $result->Fetch()) {
            if (!isset($row['PHONE']) || !is_array($row['PHONE'])) {
                $row['PHONE'] = [];
            }
            $rows[] = $row;
        }

        return $rows;
    }

    /**
     * @param array<string, mixed> $fields
     * @return array<string, mixed>
     */
    private function toCrmFields(array $fields): array
    {
        if (isset($fields['PHONE'][0]['VALUE']) && is_string($fields['PHONE'][0]['VALUE'])) {
            $fields['FM'] = [
                'PHONE' => [
                    'n0' => [
                        'VALUE' => $fields['PHONE'][0]['VALUE'],
                        'VALUE_TYPE' => is_string($fields['PHONE'][0]['VALUE_TYPE'] ?? null)
                            ? $fields['PHONE'][0]['VALUE_TYPE']
                            : 'WORK',
                    ],
                ],
            ];
            unset($fields['PHONE']);
        }

        return $fields;
    }
}
