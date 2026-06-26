<?php

use App\Http\Controllers\Api\ComponentController;
use App\Http\Controllers\Api\DiscoveryController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\AutoScreenshotController;
use App\Http\Controllers\Api\ScreenshotController;
use App\Http\Middleware\ValidateSiteKey;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::middleware(ValidateSiteKey::class)->group(function () {
    Route::get('/components', [ComponentController::class, 'index']);
    Route::post('/discoveries', [DiscoveryController::class, 'store']);
    Route::post('/events', [EventController::class, 'store']);
    Route::post('/auto-screenshot', [AutoScreenshotController::class, 'store']);
});

// Screenshot endpoints — token-authenticated, no site key required
Route::get('/screenshot/validate', [ScreenshotController::class, 'validate']);
Route::post('/screenshot', [ScreenshotController::class, 'store']);

// Serve screenshot images to the Dashboard
Route::get('/screenshot/image/{component}/{filename}', function (string $component, string $filename) {
    $path = "screenshots/{$component}/{$filename}";
    if (!Storage::disk('local')->exists($path)) {
        abort(404);
    }
    return response(Storage::disk('local')->get($path), 200)
        ->header('Content-Type', 'image/png');
})->where('filename', '[^/]+');
