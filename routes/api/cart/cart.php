<?php

use App\Http\Controllers\Local\Cart\CartController;
use Illuminate\Support\Facades\Route;

Route::group([
    'name' => 'api.'
], function () {
    Route::group([
        'name' => 'cart.',
        'prefix' => 'cart'
    ], function () {
        Route::get('/{cart}', [CartController::class, 'show'])->name('api.cart.show');
        Route::post('/addItem', [CartController::class, 'addItem'])->name('api.cart.addItem');
        Route::delete('/removeItem', [CartController::class, 'removeItem'])->name('api.cart.removeItem');
        Route::delete('/all', [CartController::class, 'removeAll'])->name('api.cart.removeAll');

        Route::patch('/{cart}/product/quantity', [CartController::class, 'updateQuantity'])
            ->name('api.cart.updateQuantity');

        Route::patch('/{id}', [CartController::class, 'update'])
            ->name('api.cart.update')
            ->middleware('auth:api');
    });
});
