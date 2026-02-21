<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Link\Pages;

use App\MoonShine\Resources\Click\ClickResource;
use App\MoonShine\Resources\Conversion\ConversionResource;
use App\MoonShine\Resources\Link\LinkResource;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Fields\Relationships\HasMany;
use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Url;

/**
 * @extends DetailPage<LinkResource>
 */
class LinkDetailPage extends DetailPage
{
    /** @return list<FieldContract> */
    protected function fields(): iterable
    {
        return [
            ID::make(),
            Text::make('Название', 'name'),
            Text::make('Slug', 'slug'),
            Url::make('Полный URL', 'full_url'),
            Url::make('URL лендинга', 'landing_url'),
            Switcher::make('Активна', 'is_active'),
            HasMany::make('Клики', 'clicks', resource: ClickResource::class)
                ->relatedLink(),
            HasMany::make('Конверсии', 'conversions', resource: ConversionResource::class)
                ->relatedLink(),
            Date::make('Создано', 'created_at')->format('d.m.Y H:i:s'),
            Date::make('Обновлено', 'updated_at')->format('d.m.Y H:i:s'),
        ];
    }
}
