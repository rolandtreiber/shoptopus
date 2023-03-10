<?php

use App\Http\Controllers\ProductCategory\ProductCategoryController;
use Illuminate\Support\Facades\Route;

Route::group([
    'name' => 'api.',
], function () {
    Route::prefix('product_categories')->group([
        'name' => 'product_categories.',], function () {
        Route::get('/', [ProductCategoryController::class, 'getAll'])->name('api.product_categories.getAll');
    });

    Route::prefix('product_category')->group([
        'name' => 'product_category.',], function () {
        Route::get('/{id}', [ProductCategoryController::class, 'get'])->name('api.product_category.get');
        Route::get('/slug/{slug}', [ProductCategoryController::class, 'getBySlug'])->name('api.product_category.getBySlug');
    });
});
