<?php

use App\Modules\RegistrationModule\Controllers\AdminRegistrationController;
use App\Modules\RegistrationModule\Controllers\CheckoutController;
use App\Modules\RegistrationModule\Controllers\RegistrationController;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function () {
    Route::middleware(['auth', 'role:peserta'])->group(function () {
        Route::get('/checkout/{eventSlug}', [CheckoutController::class, 'create'])
            ->name('checkout.create');
        Route::post('/checkout', [CheckoutController::class, 'store'])
            ->name('checkout.store');

        Route::get('/my/registrations', [RegistrationController::class, 'index'])
            ->name('registrations.index');
        Route::get('/my/registrations/{registration}', [RegistrationController::class, 'show'])
            ->name('registrations.show');
    });

    Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/registrations', [AdminRegistrationController::class, 'index'])
            ->name('registrations.index');
    });
});
