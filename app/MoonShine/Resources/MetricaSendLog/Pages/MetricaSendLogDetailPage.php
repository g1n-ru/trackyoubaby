<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\MetricaSendLog\Pages;

use App\Models\Click;
use App\MoonShine\Resources\Click\ClickResource;
use App\MoonShine\Resources\MetricaSendLog\MetricaSendLogResource;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Json;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;

/**
 * @extends DetailPage<MetricaSendLogResource>
 */
class MetricaSendLogDetailPage extends DetailPage
{
    /** @return list<FieldContract> */
    protected function fields(): iterable
    {
        return [
            ID::make(),
            BelongsTo::make('Клик', 'click', formatted: static fn (Click $click) => $click->click_id, resource: ClickResource::class),
            Text::make('Тип события', 'event_type'),
            Json::make('Тело запроса', 'request_payload'),
            Number::make('HTTP статус', 'response_status'),
            Textarea::make('Тело ответа', 'response_body'),
            Switcher::make('Успех', 'success'),
            Number::make('Кол-во повторов', 'retry_count'),
            Textarea::make('Сообщение об ошибке', 'error_message'),
            Date::make('Создано', 'created_at')->format('d.m.Y H:i:s'),
        ];
    }
}
