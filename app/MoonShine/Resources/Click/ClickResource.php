<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Click;

use App\Models\Click;
use App\MoonShine\Resources\Click\Pages\ClickDetailPage;
use App\MoonShine\Resources\Click\Pages\ClickIndexPage;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\MenuManager\Attributes\Order;
use MoonShine\Support\Attributes\Icon;
use MoonShine\Support\Enums\Action;
use MoonShine\Support\ListOf;

/**
 * @extends ModelResource<Click, ClickIndexPage, null, ClickDetailPage>
 */
#[Icon('cursor-arrow-ripple')]
#[Order(2)]
class ClickResource extends ModelResource
{
    protected string $model = Click::class;

    protected string $title = 'Клики';

    protected string $column = 'click_id';

    protected function activeActions(): ListOf
    {
        return parent::activeActions()->only(Action::VIEW);
    }

    /** @return list<class-string<PageContract>> */
    protected function pages(): array
    {
        return [
            ClickIndexPage::class,
            ClickDetailPage::class,
        ];
    }

    protected function search(): array
    {
        return [
            'click_id',
            'ip',
            'subid',
            'ym_uid',
        ];
    }
}
