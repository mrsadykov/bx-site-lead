<?php

use Bitrix\Main\Loader;

Loader::registerAutoLoadClasses('site.lead', [
    'Site\Lead\Domain\Lead' => 'lib/Domain/Lead.php',
    'Site\Lead\Domain\LeadRepository' => 'lib/Domain/LeadRepository.php',
    'Site\Lead\Application\EventIdStore' => 'lib/Application/EventIdStore.php',
    'Site\Lead\Application\AcceptSiteEvent' => 'lib/Application/AcceptSiteEvent.php',
    'Site\Lead\Application\UpsertLeadByExternalId' => 'lib/Application/UpsertLeadByExternalId.php',
    'Site\Lead\Infrastructure\CrmLeadGateway' => 'lib/Infrastructure/CrmLeadGateway.php',
    'Site\Lead\Infrastructure\CrmLeadRepository' => 'lib/Infrastructure/CrmLeadRepository.php',
    'Site\Lead\Infrastructure\PortalCrmLeadGateway' => 'lib/Infrastructure/PortalCrmLeadGateway.php',
    'Site\Lead\Infrastructure\SqlEventIdStore' => 'lib/Infrastructure/SqlEventIdStore.php',
]);
