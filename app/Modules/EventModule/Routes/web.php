<?php

use App\Modules\EventModule\Controllers\CategoryController;
use App\Modules\EventModule\Controllers\EventAdminController;
use App\Modules\EventModule\Controllers\EventCatalogController;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function () {
    Route::get('/events', [EventCatalogController::class, 'index'])->name('events.index');
    Route::get('/events/{slug}', [EventCatalogController::class, 'show'])->name('events.show');

    Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::resource('categories', CategoryController::class)->except('show');
        Route::resource('events', EventAdminController::class);

        Route::patch('events/{event}/publish', [EventAdminController::class, 'publish'])
            ->name('events.publish');
    });
});
