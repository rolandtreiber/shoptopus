<?php

use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

Route::group([
    'name' => 'api.',
], function () {
    Route::prefix('user')->middleware('auth:api')->group([
        'name' => 'user.', ], function () {
            Route::get('/favorites', [UserController::class, 'favorites'])->name('api.user.favorites');
        });
});
