<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Click\Pages;

use App\MoonShine\Resources\Click\ClickResource;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\DateRange;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Text;

/**
 * @extends IndexPage<ClickResource>
 */
class ClickIndexPage extends IndexPage
{
    protected bool $isLazy = true;

    /** @return list<FieldContract> */
    protected function fields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('ID клика', 'click_id'),
            Date::make('Время', 'ts_utc')->format('d.m.Y H:i:s')->sortable(),
            Text::make('IP', 'ip'),
            Text::make('SubID', 'subid'),
            Text::make('YM UID', 'ym_uid'),
            Date::make('Отправлено в YM', 'ym_sent_at')->format('d.m.Y H:i:s'),
        ];
    }

    /** @return list<FieldContract> */
    protected function filters(): iterable
    {
        return [
            Text::make('ID клика', 'click_id'),
            Text::make('IP', 'ip'),
            Text::make('SubID', 'subid'),
            Text::make('SubID2', 'subid2'),
            Text::make('SubID3', 'subid3'),
            Text::make('YM UID', 'ym_uid'),
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
}
