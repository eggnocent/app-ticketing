<?php

use App\Modules\TicketModule\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function () {
    Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::resource('tickets', TicketController::class)->except('show');
    });
});
