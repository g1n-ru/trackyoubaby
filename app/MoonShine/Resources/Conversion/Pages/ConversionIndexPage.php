<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Conversion\Pages;

use App\MoonShine\Resources\Conversion\ConversionResource;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Support\Enums\FormMethod;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\DateRange;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\RangeSlider;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Text;

/**
 * @extends IndexPage<ConversionResource>
 */
class ConversionIndexPage extends IndexPage
{
    protected bool $isLazy = true;

    /** @return list<FieldContract> */
    protected function fields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('ID клика', 'click_id'),
            Number::make('Доход', 'revenue')->sortable(),
            Text::make('Валюта', 'currency'),
            Text::make('ID заказа', 'order_id'),
            Date::make('Отправлено в YM', 'ym_sent_at')->format('d.m.Y H:i:s'),
            Date::make('Создано', 'created_at')->format('d.m.Y H:i:s')->sortable(),
        ];
    }

    /** @return list<FieldContract> */
    protected function filters(): iterable
    {
        return [
            Text::make('ID клика', 'click_id'),
            Text::make('ID заказа', 'order_id'),
            RangeSlider::make('Доход', 'revenue')
                ->min(0)
                ->max(9999999),
            Select::make('Валюта', 'currency')->options([
                'RUB' => 'RUB',
                'USD' => 'USD',
                'KZT' => 'KZT',
            ])->nullable(),
            Select::make('Отправлено в YM', 'ym_sent_at')->options([
                '1' => 'Да',
                '0' => 'Нет',
            ])->nullable()
                ->onApply(static fn ($query, $value) => $value === '1'
                    ? $query->whereNotNull('ym_sent_at')
                    : $query->whereNull('ym_sent_at')),
            DateRange::make('Дата создания', 'created_at'),
        ];
    }

    protected function topLayer(): array
    {
        return [
            ...parent::topLayer(),
            ActionButton::make('CSV Экспорт')
                ->icon('arrow-down-tray')
                ->inModal(
                    'CSV Экспорт',
                    fn (): FormBuilder => FormBuilder::make(route('tracker.conversion.export'))
                        ->method(FormMethod::GET)
                        ->fields([
                            Date::make('Дата начала', 'start_date')
                                ->required()
                                ->format('Y-m-d'),
                            Date::make('Дата окончания', 'end_date')
                                ->required()
                                ->format('Y-m-d'),
                        ])
                        ->submit('Скачать'),
                ),
        ];
    }
}
