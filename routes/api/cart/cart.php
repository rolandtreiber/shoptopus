<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Cart\CartController;

Route::group([
    'name' => 'api.',
    'prefix' => 'api',
    'middleware' => 'api'
], function () {
    Route::group([
        'name' => 'carts.',
        'prefix' => 'carts'
    ], function () {
        Route::get('/', [CartController::class, 'getAll'])->name('api.carts.getAll');
        Route::get('/{id}', [CartController::class, 'get'])->name('api.carts.get');
        Route::post('/', [CartController::class, 'post'])->name('api.carts.create');
        Route::patch('/{id}', [CartController::class, 'update'])->name('api.carts.update');
        Route::delete('/{id}',  [CartController::class, 'delete'])->name('api.carts.delete');
    });
});
