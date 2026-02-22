<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Link\Pages;

use App\Models\Link;
use App\MoonShine\Resources\Link\LinkResource;
use Illuminate\Support\Facades\Config;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;

/**
 * @extends FormPage<LinkResource>
 */
class LinkFormPage extends FormPage
{
    /** @return list<FieldContract> */
    protected function fields(): iterable
    {
        $baseUrl = Config::get('app.url', 'https://your-domain.com');

        return [
            Text::make('Название', 'name')
                ->required(),
            Text::make('Slug', 'slug')
                ->required()
                ->prefix($baseUrl.'/')
                ->default(Link::generateUniqueSlug())
                ->placeholder('landing-example')
                ->hint('Измените или оставьте предложенный вариант'),
            Text::make('URL лендинга', 'landing_url')
                ->default('https://')
                ->required(),
            Switcher::make('Активна', 'is_active')
                ->default(true),
        ];
    }

    protected function rules(DataWrapperContract $item): array
    {
        $linkId = $item->getKey();

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-z0-9\-]+(\/[a-z0-9\-]+)?$/',
                'unique:links,slug'.($linkId ? ','.$linkId : ''),
            ],
            'landing_url' => ['required', 'regex:/^https?:\/\/.+/i', 'max:2048'],
            'is_active' => ['boolean'],
        ];
    }
}
