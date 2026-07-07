<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ConnectController;

Route::get('/', HomeController::class)->name('home');

Route::post('/connect', ConnectController::class)
    ->middleware('throttle:30,1')
    ->name('connect');
