<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\UserController;

Route::group([
    'name' => 'api.'
], function () {
    Route::group([
        'name' => 'user.',
        'prefix' => 'user',
        'middleware' => 'auth:api'
    ], function () {
        Route::get('/favorites', [UserController::class, 'favorites'])->name('api.user.favorites');
    });
});
