<?php

use App\Http\Controllers\Local\Order\OrderController;
use Illuminate\Support\Facades\Route;

Route::group([
    'name' => 'api.'
], function () {
    Route::group([
        'name' => 'orders.',
        'prefix' => 'orders',
        'middleware' => 'auth:api'
    ], function () {
        Route::get('/', [OrderController::class, 'getAll'])->name('api.orders.getAll');
    });

    Route::group([
        'name' => 'order.',
        'prefix' => 'order',
        'middleware' => 'auth:api'
    ], function () {
        Route::get('/{id}', [OrderController::class, 'get'])->name('api.order.get');
    });
});
