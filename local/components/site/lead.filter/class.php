<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

class SiteLeadFilter extends CBitrixComponent
{
    public function executeComponent(): void
    {
        $request = \Bitrix\Main\Context::getCurrent()->getRequest();
        if ($request->isPost() && $request->getPost('action') === 'filter') {
            global $APPLICATION;
            $APPLICATION->RestartBuffer();
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(
                ['rows' => $this->rows((string) $request->getPost('kind'))],
                JSON_UNESCAPED_UNICODE
            );
            \CMain::FinalActions();
            die();
        }

        $this->arResult['AJAX'] = (string) $request->getRequestUri();
        $this->includeComponentTemplate();
    }

    /**
     * @return list<string>
     */
    private function rows(string $kind): array
    {
        $catalog = [
            'flat' => ['Квартира 12', 'Квартира 18'],
            'storage' => ['Кладовая 3'],
            'parking' => ['Парковка 7', 'Парковка 8'],
        ];

        return $catalog[$kind] ?? [];
    }
}
