<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\MetricaSendLog;

use App\Models\MetricaSendLog;
use App\MoonShine\Resources\MetricaSendLog\Pages\MetricaSendLogDetailPage;
use App\MoonShine\Resources\MetricaSendLog\Pages\MetricaSendLogIndexPage;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\MenuManager\Attributes\Order;
use MoonShine\Support\Attributes\Icon;
use MoonShine\Support\Enums\Action;
use MoonShine\Support\ListOf;

/**
 * @extends ModelResource<MetricaSendLog, MetricaSendLogIndexPage, null, MetricaSendLogDetailPage>
 */
#[Icon('paper-airplane')]
#[Order(5)]
class MetricaSendLogResource extends ModelResource
{
    protected string $model = MetricaSendLog::class;

    protected string $title = 'Логи Метрики';

    protected array $with = ['click'];

    protected bool $detailInModal = true;

    protected function activeActions(): ListOf
    {
        return parent::activeActions()->only(Action::VIEW);
    }

    /** @return list<class-string<PageContract>> */
    protected function pages(): array
    {
        return [
            MetricaSendLogIndexPage::class,
            MetricaSendLogDetailPage::class,
        ];
    }

    protected function search(): array
    {
        return [
            'click_id',
            'event_type',
        ];
    }
}
