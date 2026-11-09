<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/** @var CBitrixComponentTemplate $this */
/** @var array $arResult */

\Bitrix\Main\UI\Extension::load('ui.vue3');
\Bitrix\Main\Page\Asset::getInstance()->addJs($templateFolder . '/script.js');
?>
<div id="site-lead-filter" data-url="<?= htmlspecialcharsbx((string) $arResult['AJAX']) ?>">
    <label>
        Тип
        <select v-model="kind">
            <option value="flat">Квартира</option>
            <option value="storage">Кладовая</option>
            <option value="parking">Парковка</option>
        </select>
    </label>
    <div>
        <button type="button" @click="apply">Применить</button>
        <span v-if="loading">загрузка…</span>
    </div>
    <ul>
        <li v-for="row in rows" :key="row">{{ row }}</li>
    </ul>
</div>
