<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\MetricaSendLog\Pages;

use App\Models\Click;
use App\MoonShine\Resources\Click\ClickResource;
use App\MoonShine\Resources\MetricaSendLog\MetricaSendLogResource;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Support\Enums\Color;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\DateRange;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;

/**
 * @extends IndexPage<MetricaSendLogResource>
 */
class MetricaSendLogIndexPage extends IndexPage
{
    protected bool $isLazy = true;

    /** @return list<FieldContract> */
    protected function fields(): iterable
    {
        return [
            ID::make()->sortable(),
            BelongsTo::make('Клик', 'click', formatted: static fn (Click $click) => $click->click_id, resource: ClickResource::class),
            Text::make('Тип события', 'event_type')->badge(Color::PURPLE)->withoutTextWrap(),
            Number::make('HTTP статус', 'response_status'),
            Switcher::make('Успех', 'success'),
            Number::make('Повторы', 'retry_count'),
            Date::make('Создано', 'created_at')->format('d.m.Y H:i:s')->sortable(),
        ];
    }

    /**
     * @param  TableBuilder  $component
     */
    protected function modifyListComponent(ComponentContract $component): TableBuilder
    {
        return $component->columnSelection()->sticky();
    }

    /** @return list<FieldContract> */
    protected function filters(): iterable
    {
        return [
            Text::make('ID клика', 'click_id'),
            Select::make('Тип события', 'event_type')->options([
                'session_params' => 'Параметры сессии',
                'conversion' => 'Конверсия',
            ])->nullable(),
            Select::make('Успех', 'success')->options([
                '1' => 'Да',
                '0' => 'Нет',
            ])->nullable(),
            Select::make('HTTP статус', 'response_status')->options([
                '200' => '200 OK',
                '429' => '429 Too Many Requests',
                '500' => '500 Internal Server Error',
            ])->nullable(),
            Number::make('Повторы от', 'retry_count'),
            DateRange::make('Дата создания', 'created_at'),
        ];
    }
}
