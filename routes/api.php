<?php
use Illuminate\Http\Request;
use App\Http\Controllers\Api\WatcherController;
Route::apiResource('watchers', WatcherController::class);