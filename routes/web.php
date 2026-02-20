<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/landing-example', function () {
    return view('landing-example');
})->name('landing.example');
