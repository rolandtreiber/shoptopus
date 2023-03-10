<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::group([
    'name' => 'api.',
], function () {
    Route::prefix('auth')->middleware('api')->group([
        'name' => 'auth.', ], function () {
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

    Route::prefix('auth')->middleware('auth:api')->group([
        'name' => 'auth.', ], function () {
            Route::post('logout', [AuthController::class, 'logout'])->name('api.auth.logout');

            Route::get('details', [AuthController::class, 'details'])->name('api.auth.details');
        });
});
