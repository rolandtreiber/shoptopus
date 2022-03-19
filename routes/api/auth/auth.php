<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;

Route::group([
    'name' => 'api.',
    'middleware' => 'api'
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

        Route::get('login/{provider}', [AuthController::class, 'getOAuthProviderTargetUrl'])->name('api.auth.getOAuthProviderTargetUrl');
        Route::post('login/{provider}/callback', [AuthController::class, 'handleOAuthProviderCallback'])->name('api.auth.handleOAuthProviderCallback');

        Route::post('forgot-password', [AuthController::class, 'sendPasswordReset'])
            ->middleware('guest')
            ->name('password.email');

        Route::post('reset-password', [AuthController::class, 'resetPassword'])
            ->middleware('guest')
            ->name('password.update');

        Route::post('me', [AuthController::class, 'getAuthenticatedUser'])->name('api.get-authenticated-user');


        //Route::post('admin-login', [AuthController::class, 'adminApiLoginAttempt'])->name('api.login');
//        Route::post('signup', [AuthController::class, 'apiSignup'])->name('api.auth.signup');
        //Route::post('confirm-email', [AuthController::class, 'confirmEmail'])->name('api.email-confirmation');
        //Route::post('send-reset-password-email', [AuthController::class, 'resetPassword'])->name('api.reset-password');
        //Route::post('check-reset-password-token', [AuthController::class, 'checkPasswordResetToken'])->name('api.check-password-reset-token');
        //Route::post('update-password', [AuthController::class, 'updatePasswordFromResetFlow'])->name('api.update-password-from-reset-flow');
    });

    Route::group([
        'name' => 'auth.',
        'prefix' => 'auth',
        'middleware' => 'auth:api'
    ], function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('api.auth.logout')->middleware('auth:api');
    });
});
