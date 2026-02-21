<?php

declare(strict_types=1);

namespace App\MoonShine\Pages;

use App\Enums\SettingType;
use App\Models\Click;
use App\Models\Conversion;
use App\Models\MetricaSendLog;
use App\Services\SettingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Laravel\Pages\Page;
use MoonShine\MenuManager\Attributes\Order;
use MoonShine\Support\Attributes\AsyncMethod;
use MoonShine\Support\Attributes\Icon;
use MoonShine\Support\Enums\HttpMethod;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Url;
use Throwable;

#[Icon('cog-6-tooth')]
#[Order(7)]
class SettingsPage extends Page
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
        return $this->title ?: 'Настройки';
    }

    /**
     * @return list<ComponentContract>
     *
     * @throws Throwable
     */
    protected function components(): iterable
    {
        $settingService = app(SettingService::class);

        return [
            Box::make('Яндекс Метрика', [
                FormBuilder::make()
                    ->fields([
                        Text::make('ID счётчика', 'ym_counter_id')->required(),
                        Text::make('OAuth-токен', 'ym_token')->eye()->required(),
                    ])
                    ->fill([
                        'ym_counter_id' => $settingService->getYmCounterId(),
                        'ym_token' => $settingService->getYmToken(),
                    ])
                    ->asyncMethod('saveMetrica', 'Настройки Метрики сохранены')
                    ->submit('Сохранить'),
            ]),

            Box::make('Редирект', [
                FormBuilder::make()
                    ->fields([
                        Switcher::make('Редирект включён', 'redirect_enabled'),
                        Url::make('URL по умолчанию', 'default_landing_url')
                            ->required()
                            ->showWhen('redirect_enabled', 1),
                    ])
                    ->fill([
                        'default_landing_url' => $settingService->getDefaultLandingUrl(),
                        'redirect_enabled' => $settingService->isRedirectEnabled(),
                    ])
                    ->asyncMethod('save', 'Настройки сохранены')
                    ->submit('Сохранить'),
            ]),

            Box::make('Хранение данных', [
                FormBuilder::make()
                    ->fields([
                        Number::make('Срок хранения (дней)', 'data_retention_days')
                            ->min(1)
                            ->required(),
                    ])
                    ->fill([
                        'data_retention_days' => $settingService->getDataRetentionDays(),
                    ])
                    ->asyncMethod('saveRetention', 'Настройки хранения сохранены')
                    ->submit('Сохранить'),
            ]),

            Box::make('Опасная зона', [
                ActionButton::make(
                    'Обнулить данные',
                    $this->getCore()->getRouter()->getEndpoints()->method('resetData', 'Данные обнулены', page: $this),
                )
                    ->error()
                    ->icon('trash')
                    ->async(method: HttpMethod::POST)
                    ->withConfirm(
                        'Обнулить все данные?',
                        'Будут удалены все клики, конверсии и логи метрики. Это действие необратимо.',
                        'Да, обнулить',
                    ),
            ]),
        ];
    }

    /** @noinspection PhpUnused */
    #[AsyncMethod]
    public function saveMetrica(Request $request): void
    {
        $settingService = app(SettingService::class);

        $settingService->set('ym_counter_id', $request->input('ym_counter_id', ''));
        $settingService->set('ym_token', $request->input('ym_token', ''));

        session()->flash('toast', [
            'type' => 'success',
            'message' => 'Настройки Метрики сохранены',
        ]);
    }

    /** @noinspection PhpUnused */
    #[AsyncMethod]
    public function saveRetention(Request $request): void
    {
        $days = max(1, (int) $request->input('data_retention_days', 90));

        $settingService = app(SettingService::class);
        $settingService->set('data_retention_days', (string) $days, SettingType::Integer);

        session()->flash('toast', [
            'type' => 'success',
            'message' => 'Настройки хранения сохранены',
        ]);
    }

    /** @noinspection PhpUnused */
    #[AsyncMethod]
    public function resetData(): void
    {
        Schema::disableForeignKeyConstraints();
        MetricaSendLog::truncate();
        Conversion::truncate();
        Click::truncate();
        Schema::enableForeignKeyConstraints();

        session()->flash('toast', [
            'type' => 'success',
            'message' => 'Данные обнулены',
        ]);
    }

    /**
     * @noinspection PhpUnused
     * @noinspection PhpMultipleClassDeclarationsInspection
     */
    #[AsyncMethod]
    public function save(Request $request): void
    {
        $validator = Validator::make($request->all(), [
            'redirect_enabled' => ['nullable'],
            'default_landing_url' => ['required_if:redirect_enabled,1', 'nullable', 'regex:/^https?:\/\/.+/i'],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $validated = $validator->validated();

        $settingService = app(SettingService::class);

        $settingService->set('default_landing_url', $validated['default_landing_url'] ?? '');
        $settingService->set('redirect_enabled', empty($validated['redirect_enabled']) ? '0' : '1', SettingType::Boolean);

        session()->flash('toast', [
            'type' => 'success',
            'message' => 'Настройки сохранены',
        ]);
    }
}
