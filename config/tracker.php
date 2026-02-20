<?php

return [

    'metrica' => [
        'counter_id' => env('YM_COUNTER_ID'),
        'token' => env('YM_TOKEN'),
        'api_url' => env('YM_API_URL', 'https://mc.yandex.ru/collect'),
    ],

    'rate_limits' => [
        'click' => (int) env('RATE_LIMIT_CLICK', 60),
        'clientid' => (int) env('RATE_LIMIT_CLIENTID', 120),
        'conversion' => (int) env('RATE_LIMIT_CONVERSION', 30),
    ],

    'cookie' => [
        'max_age' => (int) env('TRACKER_COOKIE_MAX_AGE', 43200),
        'domain' => env('TRACKER_COOKIE_DOMAIN'),
        'secure' => (bool) env('TRACKER_COOKIE_SECURE', false),
    ],

    'retry' => [
        'max_attempts' => (int) env('RETRY_MAX_ATTEMPTS', 3),
        'initial_delay' => (int) env('RETRY_INITIAL_DELAY', 1),
        'max_delay' => (int) env('RETRY_MAX_DELAY', 10),
    ],

    'default_landing_url' => env('DEFAULT_LANDING_URL', 'https://example.com'),

    'data_retention_days' => (int) env('DATA_RETENTION_DAYS', 90),

];
