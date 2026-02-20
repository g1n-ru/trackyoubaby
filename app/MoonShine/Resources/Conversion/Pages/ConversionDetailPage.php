<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Conversion\Pages;

use App\MoonShine\Resources\Conversion\ConversionResource;
use MoonShine\Contracts\UI\FieldContract;
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
            Text::make('ID клика', 'click_id'),
            Number::make('Доход', 'revenue'),
            Text::make('Валюта', 'currency'),
            Text::make('ID заказа', 'order_id'),
            Json::make('Метаданные', 'metadata'),
            Date::make('Отправлено в YM', 'ym_sent_at')->format('d.m.Y H:i:s'),
            Date::make('Создано', 'created_at')->format('d.m.Y H:i:s'),
        ];
    }
}
