<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Conversion;

use App\Models\Conversion;
use App\MoonShine\Resources\Conversion\Pages\ConversionDetailPage;
use App\MoonShine\Resources\Conversion\Pages\ConversionIndexPage;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\MenuManager\Attributes\Order;
use MoonShine\Support\Attributes\Icon;
use MoonShine\Support\Enums\Action;
use MoonShine\Support\ListOf;

/**
 * @extends ModelResource<Conversion, ConversionIndexPage, null, ConversionDetailPage>
 */
#[Icon('banknotes')]
#[Order(4)]
class ConversionResource extends ModelResource
{
    protected string $model = Conversion::class;

    protected string $title = 'Конверсии';

    protected array $with = ['click', 'link'];

    protected bool $detailInModal = true;

    protected function activeActions(): ListOf
    {
        return parent::activeActions()->only(Action::VIEW);
    }

    /** @return list<class-string<PageContract>> */
    protected function pages(): array
    {
        return [
            ConversionIndexPage::class,
            ConversionDetailPage::class,
        ];
    }

    protected function search(): array
    {
        return [
            'click_id',
            'target',
            'order_id',
        ];
    }
}
