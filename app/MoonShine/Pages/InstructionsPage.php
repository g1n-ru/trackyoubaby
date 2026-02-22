<?php

declare(strict_types=1);

namespace App\MoonShine\Pages;

use App\Services\SettingService;
use Illuminate\Support\Facades\Config;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Laravel\Pages\Page;
use MoonShine\MenuManager\Attributes\Order;
use MoonShine\Support\Attributes\Icon;
use MoonShine\UI\Components\FlexibleRender;
use MoonShine\UI\Components\Tabs;
use MoonShine\UI\Components\Tabs\Tab;

#[Icon('book-open')]
#[Order(6)]
class InstructionsPage extends Page
{
    /** @return array<string, string> */
    public function getBreadcrumbs(): array
    {
        return [
            '#' => $this->getTitle(),
        ];
    }

    public function getTitle(): string
    {
        return $this->title ?: 'Инструкция';
    }

    /** @return list<ComponentContract> */
    protected function components(): iterable
    {
        $settingService = app(SettingService::class);
        $appUrl = Config::get('app.url', 'https://your-domain.com');
        $counterId = $settingService->getYmCounterId() ?: 'YOUR_COUNTER_ID';

        return [
            Tabs::make([
                Tab::make('Рекламная ссылка', [
                    FlexibleRender::make($this->renderAdvertisingLink($appUrl)),
                ])->icon('link'),

                Tab::make('ЧПУ-ссылки', [
                    FlexibleRender::make($this->renderShortLinksInstructions($appUrl)),
                ])->icon('link'),

                Tab::make('Сниппет на лендинге', [
                    FlexibleRender::make($this->renderSnippetInstructions($appUrl, $counterId)),
                ])->icon('code-bracket'),

                Tab::make('Конверсии', [
                    FlexibleRender::make($this->renderConversionInstructions($appUrl)),
                ])->icon('currency-dollar'),

                Tab::make('CSV-экспорт', [
                    FlexibleRender::make($this->renderExportInstructions()),
                ])->icon('arrow-down-tray'),

                Tab::make('API', [
                    FlexibleRender::make($this->renderApiEndpoints()),
                ])->icon('server'),

                Tab::make('Обслуживание', [
                    FlexibleRender::make($this->renderMaintenanceInstructions()),
                ])->icon('wrench-screwdriver'),

                Tab::make('ENV-переменные', [
                    FlexibleRender::make($this->renderEnvVariables()),
                ])->icon('cog-6-tooth'),
            ]),
        ];
    }

    private function renderAdvertisingLink(string $appUrl): string
    {
        return $this->card(
            'В Яндекс Директ указывайте ссылку вида:',
            $this->codeBlock(
                e($appUrl.'/')
                .$this->highlight('link-name')
                .e('?subid=')
                .$this->highlight('campaign_name')
            )
            .$this->table(
                ['Параметр', '', 'Описание'],
                [
                    ['<code>link-name</code>', $this->badge('обязательно'), 'Slug ссылки из раздела Ссылки'],
                    ['<code>subid</code>', $this->badge('опционально', false), 'Название кампании / источник'],
                    ['<code>subid2</code>', $this->badge('опционально', false), 'Доп. метка (объявление)'],
                    ['<code>subid3</code>', $this->badge('опционально', false), 'Доп. метка (группа)'],
                    ['<code>subid4</code>', $this->badge('опционально', false), 'Доп. метка (произвольная)'],
                ]
            )
            .$this->hint('При клике трекер записывает данные в БД, ставит cookie <code>click_id</code> и делает 302-редирект на лендинг.')
            .$this->hint($this->highlight('Выделенные значения').' — замените на свои.')
        );
    }

    private function renderShortLinksInstructions(string $appUrl): string
    {
        return $this->card(
            'Создавайте короткие ЧПУ-ссылки в разделе <strong>Ссылки</strong>, скрывая URL лендинга:',
            $this->codeBlock(
                e($appUrl.'/')
                .$this->highlight('promo-spring')
                .e('?subid=')
                .$this->highlight('campaign')
                .e('&subid2=')
                .$this->highlight('ad1')
            )
            .$this->table(
                ['Элемент', 'Описание'],
                [
                    ['<code>promo-spring</code>', 'Slug ссылки (задаётся вручную или генерируется автоматически)'],
                    ['<code>subid, subid2...</code>', 'Query-параметры передаются как обычно и записываются в клик'],
                ]
            )
            .$this->label('Как работает')
            .'<ol style="margin:0 0 16px 20px;line-height:1.8;font-size:14px;color:var(--color-base-text)">'
            .'<li>Создайте ссылку в разделе <strong>Ссылки</strong> &mdash; укажите название, URL лендинга и (опционально) slug</li>'
            .'<li>Если slug не указан, он будет сгенерирован автоматически (например, <code>bright-fox-42</code>)</li>'
            .'<li>При переходе по ссылке трекер записывает клик с привязкой к ссылке и делает 302-редирект на лендинг</li>'
            .'<li>Статистика кликов и конверсий отображается в таблице ссылок</li>'
            .'</ol>'
            .$this->hint('URL лендинга скрыт от пользователя. Параметры subid/subid2/subid3/subid4 по-прежнему передаются через query-строку.')
            .$this->hint($this->highlight('Выделенные значения').' — замените на свои.')
        );
    }

    private function renderSnippetInstructions(string $appUrl, string $counterId): string
    {
        $metrikaCode = '<script type="text/javascript">'."\n"
            .'  (function(m,e,t,r,i,k,a){'."\n"
            .'    m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};'."\n"
            .'    m[i].l=1*new Date();'."\n"
            .'    k=e.createElement(t),a=e.getElementsByTagName(t)[0],'."\n"
            .'    k.async=1,k.src=r,a.parentNode.insertBefore(k,a)'."\n"
            .'  })(window, document, "script",'."\n"
            .'    "https://mc.yandex.ru/metrika/tag.js", "ym");'."\n"
            .'  ym('.$counterId.', "init", {'."\n"
            .'    clickmap: true,'."\n"
            .'    trackLinks: true,'."\n"
            .'    accurateTrackBounce: true'."\n"
            .'  });'."\n"
            .'</script>';

        $snippetCode = '<script>window.TRACKER_BASE_URL = \''.$appUrl.'\';</script>'."\n"
            .'<script src="'.$appUrl.'/js/tracker-snippet.js"></script>';

        return $this->syntaxHighlightInit()
            .$this->card(
                'На каждом лендинге добавьте два скрипта перед <code>&lt;/body&gt;</code>:',
                $this->label('Яндекс Метрика')
                .$this->syntaxCodeBlock($metrikaCode)
                .$this->label('Трекер-сниппет')
                .$this->syntaxCodeBlock($snippetCode)
                .$this->hint('Сниппет читает cookie <code>click_id</code>, дожидается Метрику, получает <code>_ym_uid</code> и отправляет POST на <code>/clientid</code>.')
            );
    }

    private function renderConversionInstructions(string $appUrl): string
    {
        $hl = fn (string $val): string => $this->highlight($val);

        $postCode = e('curl -X POST '.$appUrl.'/conversion \\'."\n".'  -H "Content-Type: application/json" \\'."\n".'  -d \'{"click_id": "')
            .$hl('uuid-клика')
            .e('", "target": "')
            .$hl('purchase')
            .e('", "revenue": ')
            .$hl('5000')
            .e(', "currency": "')
            .$hl('RUB')
            .e('", "order_id": "')
            .$hl('ORD-123')
            .e('"}\'');

        $getCode = e($appUrl.'/conversion?click_id=')
            .$hl('uuid-клика')
            .e('&target=')
            .$hl('purchase')
            .e('&revenue=')
            .$hl('5000')
            .e('&currency=')
            .$hl('RUB')
            .e('&order_id=')
            .$hl('ORD-123');

        return $this->card(
            'Отправьте запрос при целевом действии (заявка, покупка):',
            $this->label('POST-запрос (JSON)')
            .$this->codeBlock($postCode)
            .$this->label('GET-запрос (query-параметры)')
            .$this->codeBlock($getCode)
            .$this->table(
                ['Поле', 'Тип', '', 'Описание'],
                [
                    ['<code>click_id</code>', 'UUID', $this->badge('обязательно'), 'ID клика из cookie'],
                    ['<code>target</code>', 'строка', $this->badge('обязательно'), 'Название цели конверсии (например: purchase, lead, signup)'],
                    ['<code>revenue</code>', 'число', $this->badge('опционально', false), 'Сумма конверсии'],
                    ['<code>currency</code>', 'строка (3)', $this->badge('опционально', false), 'Валюта (RUB, USD, KZT)'],
                    ['<code>order_id</code>', 'строка', $this->badge('опционально', false), 'Номер заказа'],
                ]
            )
            .$this->hint('Поле <code>target</code> используется как идентификатор цели при отправке в Яндекс Метрику.')
            .$this->hint('GET-запрос удобен для серверных интеграций и CRM, где нельзя отправить POST (например, через пиксель или редирект).')
            .$this->hint('Можно вызывать из формы на лендинге, CRM или бэкенда. Данные отправляются в Яндекс Метрику через очередь.')
            .$this->hint($this->highlight('Выделенные значения').' — замените на свои.')
        );
    }

    private function renderExportInstructions(): string
    {
        return $this->card(
            'Экспорт CSV для загрузки офлайн-конверсий в Яндекс Метрику:',
            '<p style="margin-bottom:16px;line-height:1.6">Откройте раздел <strong>Конверсии</strong> '
            .'&rarr; кнопка <strong>CSV Экспорт</strong> '
            .'&rarr; выберите диапазон дат '
            .'&rarr; <strong>Скачать</strong>.</p>'
            .$this->label('Пример файла')
            .$this->csvPreview()
            .$this->hint('Файл готов для загрузки в Яндекс Метрику без дополнительной обработки.')
        );
    }

    private function renderApiEndpoints(): string
    {
        $getBadge = '<code style="padding:2px 8px;border-radius:4px;background:rgba(16,185,129,0.15);color:#10b981;font-size:12px;font-weight:700">GET</code>';
        $postBadge = '<code style="padding:2px 8px;border-radius:4px;background:rgba(59,130,246,0.15);color:#3b82f6;font-size:12px;font-weight:700">POST</code>';

        return $this->card(
            'Все доступные эндпоинты трекера:',
            $this->table(
                ['Метод', 'URL', 'Описание', 'Rate Limit'],
                [
                    [$getBadge, '<code>/click</code>', 'Трекинг клика + редирект', '60/мин'],
                    [$getBadge, '<code>/{slug}</code>', 'ЧПУ-ссылка &rarr; редирект на лендинг', '60/мин'],
                    [$postBadge, '<code>/clientid</code>', 'Привязка YM Client ID', '120/мин'],
                    [$getBadge.' '.$postBadge, '<code>/conversion</code>', 'Фиксация конверсии', '30/мин'],
                ]
            )
        );
    }

    private function renderMaintenanceInstructions(): string
    {
        $retentionDays = app(SettingService::class)->getDataRetentionDays();

        return $this->card(
            'Команды для обслуживания системы:',
            $this->label('Запуск обработчика очереди')
            .$this->codeBlock('php artisan queue:work')
            .'<p style="margin-bottom:24px;line-height:1.6">Отправка данных в Яндекс Метрику выполняется асинхронно. '
            .'Без запущенного worker данные <strong>не будут отправлены</strong>.</p>'
            .$this->label('Очистка старых данных')
            .$this->codeBlock('php artisan tracker:cleanup')
            .'<p style="line-height:1.6">Выполняется автоматически каждый день в 03:00. '
            .'Удаляет данные старше <strong>'.$retentionDays.'</strong> дней.</p>'
        );
    }

    private function renderEnvVariables(): string
    {
        $groups = [
            'Rate Limiting' => [
                ['RATE_LIMIT_CLICK', 'Лимит /click в минуту', '60', 'tracker.rate_limits.click'],
                ['RATE_LIMIT_CLIENTID', 'Лимит /clientid в минуту', '120', 'tracker.rate_limits.clientid'],
                ['RATE_LIMIT_CONVERSION', 'Лимит /conversion в минуту', '30', 'tracker.rate_limits.conversion'],
            ],
            '<br><br>Cookie' => [
                ['TRACKER_COOKIE_MAX_AGE', 'Время жизни cookie (минуты)', '43200', 'tracker.cookie.max_age'],
            ],
            '<br><br>Прочее' => [
                ['RETRY_MAX_ATTEMPTS', 'Макс. попыток отправки в Метрику', '3', 'tracker.retry.max_attempts'],
            ],
        ];

        $thStyle = 'text-align:left;padding:10px 12px;font-weight:600;background:var(--color-base-100);border-bottom:2px solid var(--color-base-stroke)';
        $tdStyle = 'padding:10px 12px;border-bottom:1px solid var(--color-base-stroke)';

        $html = '<div style="overflow-x:auto;margin-bottom:16px">'
            .'<table style="width:100%;font-size:14px;color:var(--color-base-text);border-collapse:collapse;table-layout:fixed">'
            .'<colgroup><col style="width:25%"><col style="width:40%"><col style="width:35%"></colgroup>'
            .'<thead><tr>'
            .'<th style="'.$thStyle.'">Переменная</th>'
            .'<th style="'.$thStyle.'">Описание</th>'
            .'<th style="'.$thStyle.'">Текущее значение</th>'
            .'</tr></thead><tbody>';

        foreach ($groups as $groupName => $rows) {
            $html .= '<tr><td colspan="3" style="padding:10px 12px;font-weight:600;font-size:12px;text-transform:uppercase;letter-spacing:0.05em;color:var(--color-base-400);border-bottom:1px solid var(--color-base-stroke)">'
                .$groupName.'</td></tr>';

            foreach ($rows as $row) {
                $isMasked = $row[4] ?? false;
                $currentValue = $this->formatConfigValue(Config::get($row[3]), $isMasked);

                $html .= '<tr>'
                    .'<td style="'.$tdStyle.'"><code>'.e($row[0]).'</code></td>'
                    .'<td style="'.$tdStyle.'">'.e($row[1]).'</td>'
                    .'<td style="'.$tdStyle.'">'.$currentValue.'</td>'
                    .'</tr>';
            }
        }

        $html .= '</tbody></table></div>';

        return $this->card('Все переменные окружения, используемые трекером:', $html);
    }

    private function formatConfigValue(mixed $value, bool $masked = false): string
    {
        if ($value === null || $value === '') {
            return '<span style="color:var(--color-base-400);font-style:italic">не задано</span>';
        }

        if (is_bool($value)) {
            $label = $value ? 'true' : 'false';
            $color = $value ? 'var(--color-success)' : 'var(--color-base-400)';

            return '<code style="color:'.$color.'">'.$label.'</code>';
        }

        $stringValue = (string) $value;

        if ($masked && mb_strlen($stringValue) > 4) {
            $stringValue = mb_substr($stringValue, 0, 2)
                .str_repeat('*', mb_strlen($stringValue) - 4)
                .mb_substr($stringValue, -2);
        }

        return '<code>'.e($stringValue).'</code>';
    }

    private function card(string $description, string $body): string
    {
        $bg = 'var(--ms-card-bg-color)';
        $color = 'var(--ms-card-color)';

        return '<div style="padding:24px;border-radius:12px;background:'.$bg.';color:'.$color.';border:1px solid var(--color-base-stroke)">'
            .'<p style="margin-bottom:20px;font-size:15px;line-height:1.6">'.$description.'</p>'
            .$body
            .'</div>';
    }

    private function syntaxHighlightInit(): string
    {
        $loaderJs = <<<'JS'
if(window._hljsLoaded){hljs.highlightAll();return}window._hljsLoaded=true;var l=document.createElement('link');l.rel='stylesheet';l.href='https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/atom-one-dark.min.css';document.head.appendChild(l);var s=document.createElement('script');s.src='https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js';s.onload=function(){hljs.highlightAll()};document.head.appendChild(s)
JS;

        return '<style>.hljs{background:transparent !important;padding:0 !important}</style>'
            .'<div x-data x-init="'.e($loaderJs).'"></div>';
    }

    private function syntaxCodeBlock(string $code): string
    {
        $copyIcon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width:16px;height:16px"><path d="M7.5 3.375c0-1.036.84-1.875 1.875-1.875h.375a3.75 3.75 0 0 1 3.75 3.75v1.875C13.5 8.161 14.34 9 15.375 9h1.875A3.75 3.75 0 0 1 21 12.75v3.375C21 17.16 20.16 18 19.125 18h-9.75A1.875 1.875 0 0 1 7.5 16.125V3.375Z"/><path d="M15 5.25a5.23 5.23 0 0 0-1.279-3.434 9.768 9.768 0 0 1 6.963 6.963A5.23 5.23 0 0 0 17.25 7.5h-1.875A.375.375 0 0 1 15 7.125V5.25ZM4.875 6H6v10.125A3.375 3.375 0 0 0 9.375 19.5H16.5v1.125c0 1.035-.84 1.875-1.875 1.875h-9.75A1.875 1.875 0 0 1 3 20.625V7.875C3 6.839 3.84 6 4.875 6Z"/></svg>';
        $checkIcon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width:16px;height:16px"><path fill-rule="evenodd" d="M7.502 6h7.128A3.375 3.375 0 0 1 18 9.375v9.375a3 3 0 0 0 3-3V6.108c0-1.505-1.125-2.811-2.664-2.94a48.972 48.972 0 0 0-.673-.05A3 3 0 0 0 15 1.5h-1.5a3 3 0 0 0-2.663 1.618c-.225.015-.45.032-.673.05C8.662 3.295 7.554 4.542 7.502 6ZM13.5 3A1.5 1.5 0 0 0 12 4.5h4.5A1.5 1.5 0 0 0 15 3h-1.5Z" clip-rule="evenodd"/><path fill-rule="evenodd" d="M3 9.375C3 8.339 3.84 7.5 4.875 7.5h9.75c1.036 0 1.875.84 1.875 1.875v11.25c0 1.035-.84 1.875-1.875 1.875h-9.75A1.875 1.875 0 0 1 3 20.625V9.375Zm9.586 4.594a.75.75 0 0 0-1.172-.938l-2.476 3.096-.908-.907a.75.75 0 0 0-1.06 1.06l1.5 1.5a.75.75 0 0 0 1.116-.062l3-3.75Z" clip-rule="evenodd"/></svg>';

        return '<div style="position:relative;margin-bottom:16px" x-data="{copied:false}">'
            .'<pre style="padding:16px 48px 16px 16px;border-radius:8px;background:#282c34;overflow-x:auto;font-size:13px;line-height:1.7;border:1px solid var(--color-base-stroke);margin:0" x-ref="code">'
            .'<code class="language-html">'.e($code).'</code></pre>'
            .'<button @click="navigator.clipboard.writeText($refs.code.textContent);copied=true;setTimeout(()=>copied=false,1500)" '
            .'class="snippet-copy" '
            .'style="position:absolute;top:50%;right:8px;transform:translateY(-50%)" '
            .'type="button">'
            .'<span x-show="!copied">'.$copyIcon.'</span>'
            .'<span x-show="copied" x-cloak style="color:var(--color-success)">'.$checkIcon.'</span>'
            .'</button>'
            .'</div>';
    }

    private function codeBlock(string $code): string
    {
        $copyIcon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width:16px;height:16px"><path d="M7.5 3.375c0-1.036.84-1.875 1.875-1.875h.375a3.75 3.75 0 0 1 3.75 3.75v1.875C13.5 8.161 14.34 9 15.375 9h1.875A3.75 3.75 0 0 1 21 12.75v3.375C21 17.16 20.16 18 19.125 18h-9.75A1.875 1.875 0 0 1 7.5 16.125V3.375Z"/><path d="M15 5.25a5.23 5.23 0 0 0-1.279-3.434 9.768 9.768 0 0 1 6.963 6.963A5.23 5.23 0 0 0 17.25 7.5h-1.875A.375.375 0 0 1 15 7.125V5.25ZM4.875 6H6v10.125A3.375 3.375 0 0 0 9.375 19.5H16.5v1.125c0 1.035-.84 1.875-1.875 1.875h-9.75A1.875 1.875 0 0 1 3 20.625V7.875C3 6.839 3.84 6 4.875 6Z"/></svg>';
        $checkIcon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width:16px;height:16px"><path fill-rule="evenodd" d="M7.502 6h7.128A3.375 3.375 0 0 1 18 9.375v9.375a3 3 0 0 0 3-3V6.108c0-1.505-1.125-2.811-2.664-2.94a48.972 48.972 0 0 0-.673-.05A3 3 0 0 0 15 1.5h-1.5a3 3 0 0 0-2.663 1.618c-.225.015-.45.032-.673.05C8.662 3.295 7.554 4.542 7.502 6ZM13.5 3A1.5 1.5 0 0 0 12 4.5h4.5A1.5 1.5 0 0 0 15 3h-1.5Z" clip-rule="evenodd"/><path fill-rule="evenodd" d="M3 9.375C3 8.339 3.84 7.5 4.875 7.5h9.75c1.036 0 1.875.84 1.875 1.875v11.25c0 1.035-.84 1.875-1.875 1.875h-9.75A1.875 1.875 0 0 1 3 20.625V9.375Zm9.586 4.594a.75.75 0 0 0-1.172-.938l-2.476 3.096-.908-.907a.75.75 0 0 0-1.06 1.06l1.5 1.5a.75.75 0 0 0 1.116-.062l3-3.75Z" clip-rule="evenodd"/></svg>';

        return '<div style="position:relative;margin-bottom:16px" x-data="{copied:false}">'
            .'<pre style="padding:16px 48px 16px 16px;border-radius:8px;background:var(--color-base-100);color:var(--color-base-text);overflow-x:auto;font-size:13px;line-height:1.7;border:1px solid var(--color-base-stroke);margin:0" x-ref="code">'
            .'<code class="nohighlight">'.$code.'</code></pre>'
            .'<button @click="navigator.clipboard.writeText($refs.code.textContent);copied=true;setTimeout(()=>copied=false,1500)" '
            .'class="snippet-copy" '
            .'style="position:absolute;top:50%;right:8px;transform:translateY(-50%)" '
            .'type="button">'
            .'<span x-show="!copied">'.$copyIcon.'</span>'
            .'<span x-show="copied" x-cloak style="color:var(--color-success)">'.$checkIcon.'</span>'
            .'</button>'
            .'</div>';
    }

    /** @param list<string> $headers */
    private function table(array $headers, array $rows): string
    {
        $html = '<div style="overflow-x:auto;margin-bottom:16px">'
            .'<table style="width:100%;font-size:14px;color:var(--color-base-text);border-collapse:collapse">'
            .'<thead><tr>';

        foreach ($headers as $header) {
            $html .= '<th style="text-align:left;padding:10px 12px;font-weight:600;background:var(--color-base-100);border-bottom:2px solid var(--color-base-stroke)">'
                .$header.'</th>';
        }

        $html .= '</tr></thead><tbody>';

        foreach ($rows as $index => $cells) {
            $isLast = $index === count($rows) - 1;
            $borderStyle = $isLast ? '' : 'border-bottom:1px solid var(--color-base-stroke);';
            $html .= '<tr>';

            foreach ($cells as $cell) {
                $html .= '<td style="padding:10px 12px;'.$borderStyle.'">'.$cell.'</td>';
            }

            $html .= '</tr>';
        }

        $html .= '</tbody></table></div>';

        return $html;
    }

    private function label(string $text): string
    {
        return '<p style="margin:8px 0;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;color:var(--color-base-400)">'.$text.'</p>';
    }

    private function hint(string $text): string
    {
        return '<p style="margin-top:8px;font-size:13px;color:var(--color-base-400);line-height:1.6">'.$text.'</p>';
    }

    private function csvPreview(): string
    {
        $rows = [
            ['1039482856', 'purchase', '2026-02-18 14:23:07', '5000', 'RUB', 'ORD-1041'],
            ['7291038564', 'lead', '2026-02-19 09:11:42', '12500', 'RUB', 'ORD-1042'],
            ['3847261095', 'purchase', '2026-02-20 17:45:19', '890', 'USD', 'ORD-1043'],
        ];
        $headers = ['UserId', 'Target', 'DateTime', 'Price', 'Currency', 'OrderId'];
        $html = '<div style="overflow-x:auto;margin-bottom:16px;border:1px solid var(--color-base-stroke);border-radius:8px">'
            .'<table style="width:100%;font-size:13px;font-family:monospace;color:var(--color-base-text);border-collapse:collapse">'
            .'<thead><tr>';

        foreach ($headers as $i => $header) {
            $border = $i > 0 ? 'border-left:1px solid var(--color-base-stroke);' : '';
            $html .= '<th style="padding:8px 12px;font-weight:700;background:var(--color-base-100);border-bottom:2px solid var(--color-base-stroke);text-align:left;white-space:nowrap;'.$border.'">'
                .e($header).'</th>';
        }

        $html .= '</tr></thead><tbody>';

        foreach ($rows as $rowIndex => $cells) {
            $isLast = $rowIndex === count($rows) - 1;
            $rowBorder = $isLast ? '' : 'border-bottom:1px solid var(--color-base-stroke);';
            $html .= '<tr>';

            foreach ($cells as $i => $cell) {
                $border = $i > 0 ? 'border-left:1px solid var(--color-base-stroke);' : '';
                $html .= '<td style="padding:6px 12px;white-space:nowrap;'.$rowBorder.$border.'">'.e($cell).'</td>';
            }

            $html .= '</tr>';
        }

        $html .= '</tbody></table></div>';

        return $html;
    }

    private function badge(string $text, bool $required = true): string
    {
        $bg = $required ? 'rgba(239,68,68,0.15)' : 'rgba(156,163,175,0.2)';
        $color = $required ? '#ef4444' : 'var(--color-base-400)';

        return '<span style="display:inline-block;padding:1px 6px;border-radius:4px;font-size:11px;font-weight:600;background:'.$bg.';color:'.$color.'">'.$text.'</span>';
    }

    private function highlight(string $text): string
    {
        return '<span style="background:rgba(251,191,36,0.25);color:#f59e0b;padding:1px 4px;border-radius:3px;font-weight:600">'.$text.'</span>';
    }
}
