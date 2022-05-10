<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductAttribute\ProductAttributeController;

Route::group([
    'name' => 'api.'
], function () {
    Route::group([
        'name' => 'product_attributes.',
        'prefix' => 'product_attributes'
    ], function () {
        Route::get('/', [ProductAttributeController::class, 'getAll'])->name('api.product_attributes.getAll');
        Route::get('/{id}', [ProductAttributeController::class, 'get'])->name('api.product_attributes.get');
        Route::get('/product_category/{product_category_id}', [ProductAttributeController::class, 'getAllForProductCategory'])
            ->name('api.product_attributes.getAllForProductCategory');
    });
});
