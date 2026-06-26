<?php

use App\Http\Controllers\Api\ComponentController;
use App\Http\Controllers\Api\DiscoveryController;
use App\Http\Controllers\Api\EventController;
use App\Http\Middleware\ValidateSiteKey;
use Illuminate\Support\Facades\Route;

Route::middleware(ValidateSiteKey::class)->group(function () {
    Route::get('/components', [ComponentController::class, 'index']);
    Route::post('/discoveries', [DiscoveryController::class, 'store']);
    Route::post('/events', [EventController::class, 'store']);
});
