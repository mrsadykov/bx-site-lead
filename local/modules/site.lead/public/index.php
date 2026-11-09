<?php

use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use Site\Lead\Application\AcceptSiteEvent;
use Site\Lead\Application\UpsertLeadByExternalId;
use Site\Lead\Infrastructure\CrmLeadRepository;
use Site\Lead\Infrastructure\PortalCrmLeadGateway;
use Site\Lead\Infrastructure\SqlEventIdStore;

Loader::includeModule('site.lead');
Loader::includeModule('crm');

$receiver = new AcceptSiteEvent(
    (string) Option::get('site.lead', 'webhook_secret', ''),
    new SqlEventIdStore(),
    new UpsertLeadByExternalId(
        new CrmLeadRepository(
            new PortalCrmLeadGateway(),
            (string) Option::get('site.lead', 'originator_id', 'site'),
        ),
    ),
);

$raw = file_get_contents('php://input');
$signature = (string) ($_SERVER['HTTP_X_SIGNATURE'] ?? '');
$result = $receiver->handle(is_string($raw) ? $raw : '', $signature);

http_response_code($result['status']);
header('Content-Type: application/json; charset=utf-8');
echo json_encode($result['body'], JSON_UNESCAPED_UNICODE);
