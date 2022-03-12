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
    ], function () {

        Route::post('/addItem', [CartController::class, 'addItem'])->name('api.cart.addItem');
        Route::delete('/removeItem', [CartController::class, 'removeItem'])->name('api.cart.removeItem');

        Route::get('/{id}', [CartController::class, 'get'])->name('api.cart.get')
            ->middleware('auth:api');
        Route::patch('/{id}', [CartController::class, 'update'])->name('api.cart.update')
            ->middleware('auth:api');
    });
});
