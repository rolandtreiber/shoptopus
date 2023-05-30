<?php

use App\Http\Controllers\Product\ProductController;
use Illuminate\Support\Facades\Route;

Route::group([
    'name' => 'api.',
], function () {
    Route::prefix('products')->group([
        'name' => 'products.',], function () {
        Route::get('/', [ProductController::class, 'getAll'])->name('api.products.getAll');
    });

    Route::prefix('product')->group([
        'name' => 'product.',], function () {
        Route::get('/{id}', [ProductController::class, 'get'])->name('api.product.get');
        Route::get('/slug/{slug}', [ProductController::class, 'getBySlug'])->name('api.product.getBySlug');

        Route::get('/{product}/available-attribute-options', [ProductController::class, 'getAvailableAttributeOptionsForProduct'])->name('api.product.getAvailableAttributeOptions');

        Route::post('/{id}/favorite', [ProductController::class, 'favorite'])
            ->middleware('auth:api')
            ->name('api.product.favorite');
    });
});
