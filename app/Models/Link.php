<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Link extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'landing_url',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    protected function fullUrl(): Attribute
    {
        return Attribute::get(fn (): string => url($this->slug));
    }

    public function clicks(): HasMany
    {
        return $this->hasMany(Click::class);
    }

    public function conversions(): HasMany
    {
        return $this->hasMany(Conversion::class);
    }

    protected static function booted(): void
    {
        static::creating(function (Link $link): void {
            if (empty($link->slug)) {
                $link->slug = static::generateUniqueSlug();
            }
        });
    }

    public static function generateUniqueSlug(): string
    {
        $prefixes = [
            'go', 'get', 'try', 'best', 'top',
            'new', 'hot', 'vip', 'pro', 'max',
        ];

        $adjectives = [
            'spring', 'summer', 'autumn', 'winter', 'golden',
            'silver', 'crystal', 'velvet', 'royal', 'cosmic',
            'solar', 'bright', 'prime', 'turbo', 'ultra',
            'mega', 'super', 'hyper', 'magic', 'smart',
            'rapid', 'grand', 'noble', 'fresh', 'lucky',
        ];

        $nouns = [
            'promo', 'offer', 'deal', 'sale', 'gift',
            'boost', 'spark', 'pulse', 'wave', 'storm',
            'bloom', 'orbit', 'quest', 'trail', 'vault',
            'drive', 'forge', 'haven', 'ridge', 'crest',
        ];

        $patterns = [
            fn () => $adjectives[array_rand($adjectives)].'-'.$nouns[array_rand($nouns)],
            fn () => $prefixes[array_rand($prefixes)].'/'.$adjectives[array_rand($adjectives)].'-'.$nouns[array_rand($nouns)],
            fn () => $adjectives[array_rand($adjectives)].'-'.$nouns[array_rand($nouns)].'/'.$prefixes[array_rand($prefixes)],
        ];

        do {
            $slug = $patterns[array_rand($patterns)]();
        } while (static::where('slug', $slug)->exists());

        return $slug;
    }
}
