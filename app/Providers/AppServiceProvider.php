<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        RateLimiter::for('click', function (Request $request) {
            return Limit::perMinute(Config::get('tracker.rate_limits.click'))
                ->by($request->ip());
        });

        RateLimiter::for('clientid', function (Request $request) {
            return Limit::perMinute(Config::get('tracker.rate_limits.clientid'))
                ->by($request->ip());
        });

        RateLimiter::for('conversion', function (Request $request) {
            return Limit::perMinute(Config::get('tracker.rate_limits.conversion'))
                ->by($request->ip());
        });
    }
}
