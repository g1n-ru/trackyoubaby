<?php

use App\Http\Controllers\ClickController;
use App\Http\Controllers\LinkRedirectController;
use Illuminate\Support\Facades\Route;

Route::get('/', ClickController::class);

Route::get('/landing-example', fn () => view('landing-example'))
    ->name('landing.example');

Route::middleware('throttle:click')
    ->get('/{slug}', LinkRedirectController::class)
    ->where('slug', '[a-z0-9\-]+(\/[a-z0-9\-]+)?');

Route::fallback(ClickController::class);
