<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Conversion extends Model
{
    protected $fillable = [
        'click_id',
        'link_id',
        'target',
        'revenue',
        'currency',
        'order_id',
        'ym_sent_at',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'revenue' => 'decimal:2',
            'metadata' => 'array',
            'ym_sent_at' => 'datetime',
        ];
    }

    public function click(): BelongsTo
    {
        return $this->belongsTo(Click::class, 'click_id', 'click_id');
    }

    public function link(): BelongsTo
    {
        return $this->belongsTo(Link::class);
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
