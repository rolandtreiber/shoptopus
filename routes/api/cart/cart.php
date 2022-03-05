<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Cart\CartController;

Route::group([
    'name' => 'api.',
    'prefix' => 'api',
    'middleware' => 'api'
], function () {
    Route::group([
        'name' => 'cart.',
        'prefix' => 'cart',
        'middleware' => 'auth:api'
    ], function () {
        Route::get('/{id}', [CartController::class, 'get'])->name('api.cart.get');
        Route::patch('/{id}', [CartController::class, 'update'])->name('api.cart.update');
    });
});
