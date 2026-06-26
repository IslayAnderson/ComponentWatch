<?php

use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\ComponentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ScreenshotTokenController;
use App\Http\Controllers\SiteController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return redirect()->route('sites.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('sites', SiteController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::resource('sites.components', ComponentController::class)->except(['show']);
    Route::get('sites/{site}/components/{component}/analytics', [AnalyticsController::class, 'show'])
        ->name('sites.components.analytics');
    Route::post('sites/{site}/components/{component}/screenshot-token', [ScreenshotTokenController::class, 'store'])
        ->name('sites.components.screenshot-token');
});

require __DIR__.'/auth.php';
