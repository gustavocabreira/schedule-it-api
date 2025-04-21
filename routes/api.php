<?php

declare(strict_types=1);

use App\Http\Controllers\Api\Auth\PasswordRecoveryController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function () {
    Route::prefix('auth')->name('auth.')->group(function () {
        Route::controller(AuthController::class)->group(function () {
            Route::post('register', 'register')->name('register');
            Route::post('login', 'login')->name('login');
        });

        Route::post('password-recovery/token', [PasswordRecoveryController::class, 'generateToken'])->name('password-recovery.generate-token');
        Route::get('password-recovery/{passwordRecoveryToken:token}', [PasswordRecoveryController::class, 'checkToken'])->name('password-recovery.check-token');
        Route::post('password-recovery/{passwordRecoveryToken:token}/update-password', [PasswordRecoveryController::class, 'updatePassword'])->name('password-recovery.update-password');
    });
});
