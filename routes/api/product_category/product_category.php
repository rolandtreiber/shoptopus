<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductCategory\ProductCategoryController;

Route::group([
    'name' => 'api.',
    'middleware' => 'api'
], function () {
    Route::group([
        'name' => 'product_categories.',
        'prefix' => 'product_categories'
    ], function () {
        Route::get('/', [ProductCategoryController::class, 'getAll'])->name('api.product_categories.getAll');
        Route::get('/{id}', [ProductCategoryController::class, 'get'])->name('api.product_categories.get');
    });
});
