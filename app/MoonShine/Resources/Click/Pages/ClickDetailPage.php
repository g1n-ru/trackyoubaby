<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Click\Pages;

use App\Models\Link;
use App\MoonShine\Resources\Click\ClickResource;
use App\MoonShine\Resources\Conversion\ConversionResource;
use App\MoonShine\Resources\Link\LinkResource;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Fields\Relationships\HasMany;
use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Json;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;

/**
 * @extends DetailPage<ClickResource>
 */
class ClickDetailPage extends DetailPage
{
    /** @return list<FieldContract> */
    protected function fields(): iterable
    {
        return [
            ID::make(),
            BelongsTo::make('Ссылка', 'link', formatted: static fn (Link $link) => $link->name, resource: LinkResource::class),
            Text::make('ID клика', 'click_id'),
            Date::make('Время', 'ts_utc')->format('d.m.Y H:i:s'),
            Text::make('IP', 'ip'),
            Textarea::make('User Agent', 'user_agent'),
            Textarea::make('Реферер', 'referer'),
            Text::make('SubID', 'subid'),
            Text::make('SubID2', 'subid2'),
            Text::make('SubID3', 'subid3'),
            Text::make('SubID4', 'subid4'),
            Text::make('URL лендинга', 'landing_url'),
            Json::make('Параметры запроса', 'raw_query_json'),
            Text::make('YM UID', 'ym_uid'),
            Date::make('Отправлено в YM', 'ym_sent_at')->format('d.m.Y H:i:s'),
            Date::make('Создано', 'created_at')->format('d.m.Y H:i:s'),
            HasMany::make('Конверсии', 'conversions', resource: ConversionResource::class)
                ->relatedLink(),
        ];
    }
}
