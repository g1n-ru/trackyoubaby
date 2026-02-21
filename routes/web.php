<?php

use App\Http\Controllers\ClickController;
use Illuminate\Support\Facades\Route;

Route::get('/', ClickController::class);

Route::fallback(ClickController::class);

Route::get('/landing-example', function () {
    return view('landing-example');
})->name('landing.example');
