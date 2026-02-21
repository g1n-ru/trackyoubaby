# TrackYouBaby

Трекинг кликов из Яндекс Директ с привязкой Yandex Metrica Client ID, фиксацией конверсий и CSV-экспортом офлайн-конверсий.

**Стек:** Laravel 12, MoonShine 4, MySQL, Redis, Nginx, Docker

## Требования

- Docker & Docker Compose
- Домен с DNS A-записью, указывающей на сервер

## Установка

```bash
git clone <repo-url> /home/trackyoubaby
cd /home/trackyoubaby
cp .env.example .env
```

Заполните `.env`:

```
APP_DOMAIN=your-domain.com
APP_KEY=               # сгенерируется ниже
DB_PASSWORD=secret
```

Запуск:

```bash
docker compose build
docker compose up -d mysql redis
bash docker/init-letsencrypt.sh
docker compose up -d
docker compose exec app php artisan key:generate
```

Приложение доступно по `https://your-domain.com`.

## Переменные окружения

| Переменная | Описание | По умолчанию |
|---|---|---|
| `APP_DOMAIN` | Домен приложения | — |
| `DB_PASSWORD` | Пароль MySQL root | — |
| `RATE_LIMIT_CLICK` | Лимит запросов /click в минуту | 60 |
| `RATE_LIMIT_CLIENTID` | Лимит запросов /clientid в минуту | 120 |
| `RATE_LIMIT_CONVERSION` | Лимит запросов /conversion в минуту | 30 |
| `TRACKER_COOKIE_MAX_AGE` | Время жизни cookie click_id (минуты) | 43200 |
| `RETRY_MAX_ATTEMPTS` | Макс. попыток отправки в Метрику | 3 |

## Docker-сервисы

| Сервис | Образ | Назначение |
|--------|-------|------------|
| `app` | PHP 8.3-FPM Alpine | PHP-FPM, миграции, оптимизация |
| `nginx` | nginx:alpine | Веб-сервер, SSL termination |
| `mysql` | mysql:8.4 | База данных |
| `redis` | redis:7-alpine | Кеш, очередь, сессии |
| `worker` | = app | Обработка очереди |
| `scheduler` | = app | Cron-задачи Laravel |
| `certbot` | certbot/certbot | SSL-сертификат Let's Encrypt |

## Управление

```bash
docker compose ps                  # статус сервисов
docker compose logs app            # логи приложения
docker compose logs worker         # логи очереди
docker compose exec app php artisan migrate  # миграции
docker compose up --scale worker=3 # масштабирование worker
docker compose down                # остановить всё
```

SSL-сертификат обновляется автоматически сервисом certbot.

## Использование

### 1. Рекламная ссылка

В Яндекс Директ указывайте URL:

```
https://your-domain.com/click?subid=campaign_name&landing=https://your-landing.com/page
```

Параметры:
- `subid` — название кампании / источник
- `subid2`, `subid3`, `subid4` — дополнительные метки
- `landing` — URL лендинга для редиректа (если не указан — URL из настроек админки)

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

Сниппет автоматически читает cookie `click_id`, дожидается Яндекс Метрику, получает `_ym_uid` и отправляет POST на `/clientid` для привязки.

### 3. Фиксация конверсии

```bash
curl -X POST https://your-domain.com/conversion \
  -H "Content-Type: application/json" \
  -d '{"click_id":"uuid","revenue":5000,"currency":"RUB","order_id":"ORD-123"}'
```

### 4. CSV-экспорт офлайн-конверсий

```
GET /conversion/export?start_date=2026-02-01&end_date=2026-02-20
```

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

## Очистка старых данных

Автоматически каждый день в 03:00 через scheduler. Или вручную:

```bash
docker compose exec app php artisan tracker:cleanup
```

Удаляет данные старше срока хранения, указанного в настройках админки.

## Пример лендинга

Доступен по адресу `/landing-example` — демонстрация интеграции Яндекс Метрики и трекер-сниппета с кнопкой конверсии.
