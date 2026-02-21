<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Conversion\Pages;

use App\Models\Click;
use App\Models\Link;
use App\MoonShine\Resources\Click\ClickResource;
use App\MoonShine\Resources\Conversion\ConversionResource;
use App\MoonShine\Resources\Link\LinkResource;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Json;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Text;

/**
 * @extends DetailPage<ConversionResource>
 */
class ConversionDetailPage extends DetailPage
{
    /** @return list<FieldContract> */
    protected function fields(): iterable
    {
        return [
            ID::make(),
            BelongsTo::make('Ссылка', 'link', formatted: static fn (Link $link) => $link->name, resource: LinkResource::class),
            BelongsTo::make('Клик', 'click', formatted: static fn (Click $click) => $click->click_id, resource: ClickResource::class),
            Text::make('Цель', 'target'),
            Number::make('Доход', 'revenue'),
            Text::make('Валюта', 'currency'),
            Text::make('ID заказа', 'order_id'),
            Json::make('Метаданные', 'metadata'),
            Date::make('Отправлено в YM', 'ym_sent_at')->format('d.m.Y H:i:s'),
            Date::make('Создано', 'created_at')->format('d.m.Y H:i:s'),
        ];
    }
}
