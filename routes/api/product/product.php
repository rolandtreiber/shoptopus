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
    });

    Route::group([
        'name' => 'product.',
        'prefix' => 'product'
    ], function () {
        Route::get('/{id}', [ProductController::class, 'get'])->name('api.product.get');
        Route::get('/slug/{slug}', [ProductController::class, 'getBySlug'])->name('api.product.getBySlug');

        Route::post('/{id}/favorite', [ProductController::class, 'favorite'])
            ->middleware('auth:api')
            ->name('api.product.favorite');
    });
});
