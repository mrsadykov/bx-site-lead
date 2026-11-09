<?php

declare(strict_types=1);

namespace Site\Lead\Infrastructure;

interface CrmLeadGateway
{
    /**
     * @param array<string, mixed> $params
     */
    public function call(string $method, array $params): mixed;
}
