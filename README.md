# Приём откликов с сайта

Модуль `site.lead` для коробочного Битрикса. Сайт шлёт `POST /sync/events.php` с телом JSON и заголовком `X-Signature`: HMAC-SHA256 тела на секрете из настроек модуля.

Один `event_id` принимается один раз. Один `external_id` — один лид: повтор новым событием дописывает комментарий в `COMMENTS`. Номер пишется в `ORIGINATOR_ID` и `ORIGIN_ID`.

## Установка

Скопировать `local/` и `sync/` в корень портала. Поставить модуль «Приём откликов с сайта». В настройках модуля задать секрет подписи и `ORIGINATOR_ID`.

На странице портала:

```php
<?php
$APPLICATION->IncludeComponent('site:lead.filter', '', []);
```

## Проверка

```bash
composer install
vendor/bin/phpunit
```
