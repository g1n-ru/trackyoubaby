<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Click\Pages;

use App\Models\Link;
use App\MoonShine\Resources\Click\ClickResource;
use App\MoonShine\Resources\Conversion\ConversionResource;
use App\MoonShine\Resources\Link\LinkResource;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Fields\Relationships\HasMany;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\UI\Components\Table\TableBuilder;
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
            BelongsTo::make('Ссылка', 'link', formatted: static fn (Link $link) => $link->name, resource: LinkResource::class)->badge(),
            HasMany::make('Конверсии', 'conversions', resource: ConversionResource::class)->relatedLink(),
            Text::make('ID клика', 'click_id')->withoutTextWrap(),
            Date::make('Время', 'ts_utc')->format('d.m.Y H:i:s')->sortable(),
            Text::make('IP', 'ip')->withoutTextWrap(),
            Text::make('Referer', 'referer')->withoutTextWrap(),
            Text::make('User Agent', 'user_agent')->withoutTextWrap(),
            Text::make('SubID', 'subid')->withoutTextWrap(),
            Text::make('SubID2', 'subid2')->withoutTextWrap()->columnSelection(hideOnInit: true),
            Text::make('SubID3', 'subid3')->withoutTextWrap()->columnSelection(hideOnInit: true),
            Text::make('SubID4', 'subid4')->withoutTextWrap()->columnSelection(hideOnInit: true),
            Text::make('YM UID', 'ym_uid')->withoutTextWrap(),
            Date::make('Отправлено в YM', 'ym_sent_at')->format('d.m.Y H:i:s'),
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
