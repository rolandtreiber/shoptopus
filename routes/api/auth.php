<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'apiLoginAttempt'])->name('api.login');
Route::post('admin-login', [AuthController::class, 'adminApiLoginAttempt'])->name('api.login');
Route::post('signup', [AuthController::class, 'apiSignup'])->name('api.signup');
Route::post('confirm-email', [AuthController::class, 'confirmEmail'])->name('api.email-confirmation');
Route::post('send-reset-password-email', [AuthController::class, 'resetPassword'])->name('api.reset-password');
Route::post('check-reset-password-token', [AuthController::class, 'checkPasswordResetToken'])->name('api.check-password-reset-token');
Route::post('update-password', [AuthController::class, 'updatePasswordFromResetFlow'])->name('api.update-password-from-reset-flow');
