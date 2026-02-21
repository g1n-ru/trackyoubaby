<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Click extends Model
{
    protected $fillable = [
        'click_id',
        'link_id',
        'ts_utc',
        'ip',
        'user_agent',
        'referer',
        'subid',
        'subid2',
        'subid3',
        'subid4',
        'landing_url',
        'raw_query_json',
        'ym_uid',
        'ym_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'ts_utc' => 'datetime',
            'raw_query_json' => 'array',
            'ym_sent_at' => 'datetime',
        ];
    }

    public function link(): BelongsTo
    {
        return $this->belongsTo(Link::class);
    }

    public function conversions(): HasMany
    {
        return $this->hasMany(Conversion::class, 'click_id', 'click_id');
    }

    public function metricaSendLogs(): HasMany
    {
        return $this->hasMany(MetricaSendLog::class, 'click_id', 'click_id');
    }

    public function scopeWithYmUid(Builder $query): Builder
    {
        return $query->whereNotNull('ym_uid');
    }

    public function scopeWithoutYmUid(Builder $query): Builder
    {
        return $query->whereNull('ym_uid');
    }

    public function scopeSentToMetrica(Builder $query): Builder
    {
        return $query->whereNotNull('ym_sent_at');
    }

    public function scopeNotSentToMetrica(Builder $query): Builder
    {
        return $query->whereNull('ym_sent_at');
    }
}
