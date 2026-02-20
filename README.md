# Yandex Tracker

Трекинг кликов из Яндекс Директ с привязкой Yandex Metrica Client ID, фиксацией конверсий и CSV-экспортом офлайн-конверсий.

**Стек:** Laravel 12, MoonShine 4, MySQL

## Установка

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
```

## Настройка

В `.env` заполните:

```
YM_COUNTER_ID=12345678
YM_TOKEN=your_oauth_token
DEFAULT_LANDING_URL=https://your-landing.com
```

| Переменная | Описание | По умолчанию |
|---|---|---|
| `YM_COUNTER_ID` | ID счётчика Яндекс Метрики | — |
| `YM_TOKEN` | OAuth-токен Яндекс Метрики | — |
| `YM_API_URL` | URL API Метрики | `https://mc.yandex.ru/collect` |
| `RATE_LIMIT_CLICK` | Лимит запросов /click в минуту | 60 |
| `RATE_LIMIT_CLIENTID` | Лимит запросов /clientid в минуту | 120 |
| `RATE_LIMIT_CONVERSION` | Лимит запросов /conversion в минуту | 30 |
| `TRACKER_COOKIE_MAX_AGE` | Время жизни cookie click_id (минуты) | 43200 |
| `TRACKER_COOKIE_DOMAIN` | Домен cookie | — |
| `TRACKER_COOKIE_SECURE` | Secure-флаг cookie | false |
| `RETRY_MAX_ATTEMPTS` | Макс. попыток отправки в Метрику | 3 |
| `DEFAULT_LANDING_URL` | URL лендинга по умолчанию | `https://example.com` |
| `DATA_RETENTION_DAYS` | Хранение данных (дней) | 90 |

## Использование

### 1. Рекламная ссылка

В Яндекс Директ указывайте URL:

```
https://your-domain.com/click?subid=campaign_name&landing=https://your-landing.com/page
```

Параметры:
- `subid` — название кампании / источник
- `subid2`, `subid3`, `subid4` — дополнительные метки
- `landing` — URL лендинга для редиректа (если не указан — `DEFAULT_LANDING_URL`)

При клике трекер записывает данные в БД, ставит cookie `click_id` и делает 302-редирект на лендинг.

### 2. Подключение сниппета на лендинге

На каждом лендинге добавьте два скрипта:

```html
<!-- Яндекс Метрика -->
<script type="text/javascript">
    (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
    m[i].l=1*new Date();
    k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
    (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");
    ym(YOUR_COUNTER_ID, "init", { clickmap:true, trackLinks:true, accurateTrackBounce:true });
</script>

<!-- Трекер-сниппет -->
<script>
    window.TRACKER_BASE_URL = 'https://your-domain.com';
</script>
<script src="https://your-domain.com/js/tracker-snippet.js"></script>
```

`TRACKER_BASE_URL` — базовый URL Laravel-приложения. Сниппет автоматически читает cookie `click_id`, дожидается Яндекс Метрику, получает `_ym_uid` и отправляет POST на `/clientid` для привязки (с retry при ошибке).

### 3. Фиксация конверсии

POST-запрос при целевом действии (заявка, покупка):

```bash
curl -X POST https://your-domain.com/conversion \
  -H "Content-Type: application/json" \
  -d '{"click_id":"uuid","revenue":5000,"currency":"RUB","order_id":"ORD-123"}'
```

Можно вызывать из формы на лендинге, из CRM или из бэкенда.

### 4. CSV-экспорт офлайн-конверсий

```
GET /conversion/export?start_date=2026-02-01&end_date=2026-02-20
```

Скачивается CSV-файл для загрузки в Яндекс Метрику.

## API-эндпоинты

| Метод | URL | Описание |
|---|---|---|
| GET | `/click` | Трекинг клика + редирект |
| POST | `/clientid` | Привязка Yandex Metrica Client ID |
| POST | `/conversion` | Фиксация конверсии |
| GET | `/conversion/export` | CSV-экспорт конверсий |

## Админка

`/admin` — MoonShine панель:

- **Dashboard** — метрики: Total Clicks, Total Conversions, Conversion Rate, Total Revenue, Clicks Today, Metrica Success Rate
- **Clicks** — все клики с поиском и фильтрами
- **Conversions** — конверсии с привязкой к кликам
- **Metrica Send Logs** — логи отправок в API Метрики

## Очередь

Отправка данных в Метрику идёт через очередь. Запустите worker:

```bash
php artisan queue:work
```

## Очистка старых данных

Автоматически каждый день в 03:00. Или вручную:

```bash
php artisan tracker:cleanup
```

Удаляет данные старше `DATA_RETENTION_DAYS` дней (по умолчанию 90).

## Пример лендинга

Доступен по адресу `/landing-example` — демонстрация интеграции Яндекс Метрики и трекер-сниппета с кнопкой конверсии.
