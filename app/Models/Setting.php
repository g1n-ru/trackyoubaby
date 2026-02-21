<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\SettingType;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
    ];

    protected $hidden = [
        'value',
    ];

    protected function casts(): array
    {
        return [
            'type' => SettingType::class,
        ];
    }

    public static function getValue(string $key, mixed $default = null): mixed
    {
        $setting = static::firstWhere('key', $key);

        if (! $setting) {
            return $default;
        }

        return $setting->castValue();
    }

    public static function setValue(string $key, mixed $value, SettingType $type = SettingType::String): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => (string) $value, 'type' => $type],
        );
    }

    public function castValue(): mixed
    {
        return match ($this->type) {
            SettingType::Boolean => (bool) $this->value,
            SettingType::Integer => (int) $this->value,
            default => $this->value,
        };
    }
}
