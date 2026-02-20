<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MetricaSendLog extends Model
{
    protected $fillable = [
        'click_id',
        'event_type',
        'request_payload',
        'response_status',
        'response_body',
        'success',
        'retry_count',
        'error_message',
    ];

    protected function casts(): array
    {
        return [
            'request_payload' => 'array',
            'success' => 'boolean',
            'retry_count' => 'integer',
            'response_status' => 'integer',
        ];
    }

    public function click(): BelongsTo
    {
        return $this->belongsTo(Click::class, 'click_id', 'click_id');
    }

    public function scopeSuccessful(Builder $query): Builder
    {
        return $query->where('success', true);
    }

    public function scopeFailed(Builder $query): Builder
    {
        return $query->where('success', false);
    }

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('event_type', $type);
    }
}
