<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Product\ProductController;

Route::group([
    'name' => 'api.'
], function () {
    Route::group([
        'name' => 'products.',
        'prefix' => 'products'
    ], function () {
        Route::get('/', [ProductController::class, 'getAll'])->name('api.products.getAll');
        Route::get('/{id}', [ProductController::class, 'get'])->name('api.products.get');
        Route::get('/slug/{slug}', [ProductController::class, 'getBySlug'])->name('api.products.getBySlug');
    });

    Route::group([
        'name' => 'products.',
        'prefix' => 'products',
        'middleware' => 'auth:api'
    ], function () {
        Route::post('/{id}/favorite', [ProductController::class, 'favorite'])->name('api.products.favorite');
    });
});
