<?php

use App\Http\Controllers\Local\User\UserController;
use Illuminate\Support\Facades\Route;

Route::group([
    'name' => 'api.'
], function () {
    Route::group([
        'name' => 'user.',
        'prefix' => 'user',
        'middleware' => 'auth:api'
    ], function () {
        Route::get('/favorites', [UserController::class, 'favorites'])->name('api.user.favorites');
        Route::post('/account', [UserController::class, 'updateAccountDetails'])->name('api.user.update.accountDetails');
        Route::get('/account', [UserController::class, 'getAccountDetails'])->name('api.user.get.accountDetails');
        Route::delete('/account', [UserController::class, 'deleteAccount'])->name('api.user.delete.account');
    });
});
