<?php

use App\Http\Controllers\Local\Product\ProductController;
use Illuminate\Support\Facades\Route;

Route::group([
    'name' => 'api.'
], function () {
    Route::group([
        'name' => 'products.',
        'prefix' => 'products'
    ], function () {
        Route::get('/', [ProductController::class, 'getAll'])->name('api.products.getAll');
        Route::get('/search/{search}', [ProductController::class, 'search'])->name('api.product.search');
    });

    Route::group([
        'name' => 'product.',
        'prefix' => 'product'
    ], function () {
        Route::get('/{id}', [ProductController::class, 'get'])->name('api.product.get');
        Route::get('/slug/{slug}', [ProductController::class, 'getBySlug'])->name('api.product.getBySlug');

        Route::get('/{product}/available-attribute-options', [ProductController::class, 'getAvailableAttributeOptionsForProduct'])->name('api.product.getAvailableAttributeOptions');

        Route::post('/{id}/favorite', [ProductController::class, 'favorite'])
            ->middleware('auth:api')
            ->name('api.product.favorite');

        Route::post('/{id}/rating', [ProductController::class, 'saveReview'])
            ->name('api.product.save.review');
    });
});
