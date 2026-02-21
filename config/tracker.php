<?php

return [

    'rate_limits' => [
        'click' => (int) env('RATE_LIMIT_CLICK', 60),
        'clientid' => (int) env('RATE_LIMIT_CLIENTID', 120),
        'conversion' => (int) env('RATE_LIMIT_CONVERSION', 30),
    ],

    'cookie' => [
        'max_age' => (int) env('TRACKER_COOKIE_MAX_AGE', 43200),
    ],

    'retry' => [
        'max_attempts' => (int) env('RETRY_MAX_ATTEMPTS', 3),
        'initial_delay' => (int) env('RETRY_INITIAL_DELAY', 1),
        'max_delay' => (int) env('RETRY_MAX_DELAY', 10),
    ],

];
