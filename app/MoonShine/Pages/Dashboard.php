<?php

declare(strict_types=1);

namespace App\MoonShine\Pages;

use App\Models\Click;
use App\Models\Conversion;
use App\Models\MetricaSendLog;
use Illuminate\Support\Facades\Cache;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Laravel\Pages\Page;
use MoonShine\MenuManager\Attributes\Order;
use MoonShine\Support\Attributes\Icon;
use MoonShine\Support\Enums\Color;
use MoonShine\UI\Components\Layout\Column;
use MoonShine\UI\Components\Layout\Grid;
use MoonShine\UI\Components\Metrics\Wrapped\ValueMetric;

#[Order(1)]
#[Icon('chart-bar')]
class Dashboard extends Page
{
    /** @return array<string, string> */
    public function getBreadcrumbs(): array
    {
        return [
            '#' => $this->getTitle(),
        ];
    }

    public function getTitle(): string
    {
        return $this->title ?: 'Статистика';
    }

    /** @return list<ComponentContract> */
    protected function components(): iterable
    {
        $metrics = Cache::remember('dashboard:metrics', 300, static function (): array {
            $totalClicks = Click::count();
            $totalConversions = Conversion::count();
            $totalMetricaLogs = MetricaSendLog::count();
            $successfulLogs = MetricaSendLog::successful()->count();

            return [
                'totalClicks' => $totalClicks,
                'totalConversions' => $totalConversions,
                'conversionRate' => $totalClicks > 0
                    ? round(($totalConversions / $totalClicks) * 100, 2)
                    : 0,
                'totalRevenue' => Conversion::sum('revenue'),
                'clicksToday' => Click::whereDate('created_at', today())->count(),
                'metricaSuccessRate' => $totalMetricaLogs > 0
                    ? round(($successfulLogs / $totalMetricaLogs) * 100, 2)
                    : 0,
            ];
        });

        return [
            Grid::make([
                Column::make([
                    ValueMetric::make('Всего кликов')
                        ->value($metrics['totalClicks'])
                        ->icon('cursor-arrow-ripple')
                        ->iconColor(Color::PURPLE),
                ])->columnSpan(4),

                Column::make([
                    ValueMetric::make('Всего конверсий')
                        ->value($metrics['totalConversions'])
                        ->icon('banknotes')
                        ->iconColor(Color::GREEN),
                ])->columnSpan(4),

                Column::make([
                    ValueMetric::make('Конверсия')
                        ->value($metrics['conversionRate'])
                        ->valueFormat('{value}%')
                        ->icon('chart-bar')
                        ->iconColor(Color::BLUE),
                ])->columnSpan(4),
            ]),

            Grid::make([
                Column::make([
                    ValueMetric::make('Общий доход')
                        ->value(number_format((float)$metrics['totalRevenue'], 2))
                        ->icon('currency-dollar')
                        ->iconColor(Color::SUCCESS),
                ])->columnSpan(4),

                Column::make([
                    ValueMetric::make('Кликов сегодня')
                        ->value($metrics['clicksToday'])
                        ->icon('clock')
                        ->iconColor(Color::WARNING),
                ])->columnSpan(4),

                Column::make([
                    ValueMetric::make('Успех Метрики')
                        ->value($metrics['metricaSuccessRate'])
                        ->valueFormat('{value}%')
                        ->icon('paper-airplane')
                        ->iconColor(Color::INFO),
                ])->columnSpan(4),
            ]),
        ];
    }
}
