<?php

use Bitrix\Main\Config\Option;

$moduleId = 'site.lead';

if (!$USER->IsAdmin()) {
    return;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && check_bitrix_sessid()) {
    Option::set($moduleId, 'webhook_secret', trim((string) ($_POST['webhook_secret'] ?? '')));
    Option::set($moduleId, 'originator_id', trim((string) ($_POST['originator_id'] ?? 'site')));
}

$secret = (string) Option::get($moduleId, 'webhook_secret', '');
$originator = (string) Option::get($moduleId, 'originator_id', 'site');

$tabs = [[
    'DIV' => 'edit1',
    'TAB' => 'Приём',
    'TITLE' => 'Приём откликов',
]];
$tabControl = new CAdminTabControl('siteLeadTab', $tabs);
?>
<form method="post" action="<?= $APPLICATION->GetCurPage() ?>?mid=<?= urlencode($moduleId) ?>&lang=<?= LANGUAGE_ID ?>">
    <?= bitrix_sessid_post() ?>
    <?php $tabControl->Begin(); ?>
    <?php $tabControl->BeginNextTab(); ?>
    <tr>
        <td width="40%">Секрет подписи</td>
        <td><input type="text" name="webhook_secret" value="<?= htmlspecialcharsbx($secret) ?>" size="40"></td>
    </tr>
    <tr>
        <td>ORIGINATOR_ID</td>
        <td><input type="text" name="originator_id" value="<?= htmlspecialcharsbx($originator) ?>" size="20"></td>
    </tr>
    <?php $tabControl->Buttons(); ?>
    <input type="submit" name="save" value="Сохранить" class="adm-btn-save">
    <?php $tabControl->End(); ?>
</form>
