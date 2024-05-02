<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;

Route::group([
    'name' => 'api.'
], function () {
    Route::group([
        'name' => 'auth.',
        'prefix' => 'auth',
        'middleware' => 'api'
    ], function () {
        Route::post('login', [AuthController::class, 'login'])->name('api.auth.login');
        Route::post('register', [AuthController::class, 'register'])->name('api.auth.register');

        Route::post('verify/resend', [AuthController::class, 'resendVerification'])->name('verification.resend');
        Route::get('verify/{id}', [AuthController::class, 'verify'])->name('verification.verify');

        Route::post('forgot-password', [AuthController::class, 'sendPasswordReset'])
            ->middleware('guest')
            ->name('password.email');

        Route::post('reset-password', [AuthController::class, 'resetPassword'])
            ->middleware('guest')
            ->name('password.update');

        Route::post('me', [AuthController::class, 'getAuthenticatedUser'])->name('api.get-authenticated-user');
    });

    Route::group([
        'name' => 'auth.',
        'prefix' => 'auth',
        'middleware' => 'auth:api'
    ], function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('api.auth.logout');

        Route::get('details', [AuthController::class, 'details'])->name('api.auth.details');
    });
});
