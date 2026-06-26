<?php

use App\Http\Controllers\Api\NewHashNotificationController;
use Illuminate\Support\Facades\Route;

Route::post('/internal/new-hash', [NewHashNotificationController::class, 'store']);
