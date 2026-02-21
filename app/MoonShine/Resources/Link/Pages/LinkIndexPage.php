<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Link\Pages;

use App\MoonShine\Resources\Click\ClickResource;
use App\MoonShine\Resources\Conversion\ConversionResource;
use App\MoonShine\Resources\Link\LinkResource;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Fields\Relationships\HasMany;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\UI\Components\Snippet;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\DateRange;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;

/**
 * @extends IndexPage<LinkResource>
 */
class LinkIndexPage extends IndexPage
{
    protected bool $isLazy = true;

    /** @return list<FieldContract> */
    protected function fields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('Название', 'name'),
            Text::make('URL', 'full_url')
                ->withoutTextWrap()
                ->changePreview(static fn (?string $value): string => $value
                    ? (string) Snippet::make($value)
                    : ''),
            Switcher::make('Активна', 'is_active')
                ->updateOnPreview(),
            HasMany::make('Клики', 'clicks', resource: ClickResource::class)
                ->relatedLink(),
            HasMany::make('Конверсии', 'conversions', resource: ConversionResource::class)
                ->relatedLink(),
            Date::make('Создано', 'created_at')
                ->format('d.m.Y H:i:s')
                ->sortable(),
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
            Select::make('Активна', 'is_active')->options([
                '1' => 'Да',
                '0' => 'Нет',
            ])->nullable(),
            DateRange::make('Дата создания', 'created_at'),
        ];
    }
}
