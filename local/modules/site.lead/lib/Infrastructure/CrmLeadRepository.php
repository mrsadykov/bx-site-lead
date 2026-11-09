<?php

declare(strict_types=1);

namespace Site\Lead\Infrastructure;

use Site\Lead\Domain\Lead;
use Site\Lead\Domain\LeadRepository;

final class CrmLeadRepository implements LeadRepository
{
    public function __construct(
        private readonly CrmLeadGateway $crm,
        private readonly string $originator = 'site',
    ) {
    }

    public function findByExternalId(string $externalId): ?Lead
    {
        $rows = $this->crm->call('crm.lead.list', [
            'filter' => [
                'ORIGINATOR_ID' => $this->originator,
                'ORIGIN_ID' => $externalId,
            ],
            'select' => ['ID', 'NAME', 'PHONE', 'ORIGIN_ID', 'COMMENTS'],
        ]);

        if (!is_array($rows) || !isset($rows[0]) || !is_array($rows[0])) {
            return null;
        }

        return $this->mapRow($rows[0]);
    }

    public function save(Lead $lead): Lead
    {
        $fields = $this->fields($lead);

        if ($lead->id === null) {
            $id = (int) $this->crm->call('crm.lead.add', ['fields' => $fields]);

            return new Lead($lead->externalId, $lead->name, $lead->phone, $lead->comments, $id);
        }

        $this->crm->call('crm.lead.update', [
            'id' => $lead->id,
            'fields' => $fields,
        ]);

        return $lead;
    }

    /**
     * @return array<string, mixed>
     */
    private function fields(Lead $lead): array
    {
        return [
            'TITLE' => 'Отклик ' . $lead->name,
            'NAME' => $lead->name,
            'ORIGINATOR_ID' => $this->originator,
            'ORIGIN_ID' => $lead->externalId,
            'PHONE' => [['VALUE' => $lead->phone, 'VALUE_TYPE' => 'WORK']],
            'COMMENTS' => implode("\n", $lead->comments),
        ];
    }

    /**
     * @param array<string, mixed> $row
     */
    private function mapRow(array $row): Lead
    {
        $phone = '';
        if (isset($row['PHONE'][0]['VALUE']) && is_string($row['PHONE'][0]['VALUE'])) {
            $phone = $row['PHONE'][0]['VALUE'];
        }

        $comments = [];
        if (isset($row['COMMENTS']) && is_string($row['COMMENTS']) && $row['COMMENTS'] !== '') {
            $comments = explode("\n", $row['COMMENTS']);
        }

        return new Lead(
            (string) $row['ORIGIN_ID'],
            (string) $row['NAME'],
            $phone,
            $comments,
            (int) $row['ID'],
        );
    }
}
