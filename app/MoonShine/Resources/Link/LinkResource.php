<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Link;

use App\Models\Link;
use App\MoonShine\Resources\Link\Pages\LinkDetailPage;
use App\MoonShine\Resources\Link\Pages\LinkFormPage;
use App\MoonShine\Resources\Link\Pages\LinkIndexPage;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\MenuManager\Attributes\Order;
use MoonShine\Support\Attributes\Icon;

/**
 * @extends ModelResource<Link, LinkIndexPage, LinkFormPage, LinkDetailPage>
 */
#[Icon('link')]
#[Order(2)]
class LinkResource extends ModelResource
{
    protected string $model = Link::class;

    protected string $title = 'Ссылки';

    protected string $column = 'name';

    protected bool $detailInModal = true;

    /** @return list<class-string<PageContract>> */
    protected function pages(): array
    {
        return [
            LinkIndexPage::class,
            LinkFormPage::class,
            LinkDetailPage::class,
        ];
    }

    protected function search(): array
    {
        return [
            'name',
            'slug',
        ];
    }
}
