<?php

use Bitrix\Main\Application;
use Bitrix\Main\ModuleManager;

class site_lead extends CModule
{
    public $MODULE_ID = 'site.lead';
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME = 'Приём откликов с сайта';
    public $MODULE_DESCRIPTION = 'Один внешний номер — один лид. Повтор события не создаёт вторую карточку.';

    public function __construct()
    {
        $arModuleVersion = [];
        include __DIR__ . '/version.php';

        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
    }

    public function DoInstall(): void
    {
        ModuleManager::registerModule($this->MODULE_ID);
        Application::getConnection()->queryExecute(
            (string) file_get_contents(__DIR__ . '/db/mysql/install.sql')
        );
    }

    public function DoUninstall(): void
    {
        Application::getConnection()->queryExecute(
            (string) file_get_contents(__DIR__ . '/db/mysql/uninstall.sql')
        );
        ModuleManager::unRegisterModule($this->MODULE_ID);
    }
}
