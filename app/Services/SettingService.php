<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\SettingType;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingService
{
    private const CACHE_PREFIX = 'setting:';

    private const CACHE_TTL = 300;

    public function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember(
            self::CACHE_PREFIX.$key,
            self::CACHE_TTL,
            static fn () => Setting::getValue($key, $default),
        );
    }

    public function set(string $key, mixed $value, SettingType $type = SettingType::String): void
    {
        Setting::setValue($key, $value, $type);

        Cache::forget(self::CACHE_PREFIX.$key);
    }

    public function getDefaultLandingUrl(): string
    {
        return (string) $this->get('default_landing_url', '');
    }

    public function isRedirectEnabled(): bool
    {
        return (bool) $this->get('redirect_enabled', true);
    }

    public function getYmCounterId(): string
    {
        return (string) $this->get('ym_counter_id', '');
    }

    public function getYmToken(): string
    {
        return (string) $this->get('ym_token', '');
    }

    public function getDataRetentionDays(): int
    {
        return (int) $this->get('data_retention_days', 90);
    }
}
