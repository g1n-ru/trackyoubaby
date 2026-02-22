<?php

use App\Http\Controllers\ClickController;
use App\Http\Controllers\ClientIdController;
use App\Http\Controllers\ConversionController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:click')->get('/click', ClickController::class)->name('tracker.click');

Route::middleware('throttle:clientid')->post('/clientid', ClientIdController::class)->name('tracker.clientid');

Route::middleware('throttle:conversion')->match(['get', 'post'], '/conversion', [ConversionController::class, 'store'])->name('tracker.conversion.store');

Route::middleware([
    'moonshine',
    MoonShine\Laravel\Http\Middleware\Authenticate::class,
])->get('/conversion/export', [ConversionController::class, 'export'])->name('tracker.conversion.export');
