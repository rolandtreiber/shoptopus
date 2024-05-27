<?php

use App\Http\Controllers\Local\ProductAttribute\ProductAttributeController;
use Illuminate\Support\Facades\Route;

Route::group([
    'name' => 'api.'
], function () {
    Route::group([
        'name' => 'product_attributes.',
        'prefix' => 'product_attributes'
    ], function () {
        Route::get('/', [ProductAttributeController::class, 'getAll'])->name('api.product_attributes.getAll');
        Route::get('/product_category/{product_category_id}', [ProductAttributeController::class, 'getAllForProductCategory'])
            ->name('api.product_attributes.getAllForProductCategory');
    });

    Route::group([
        'name' => 'product_attribute.',
        'prefix' => 'product_attribute'
    ], function () {
        Route::get('/{id}', [ProductAttributeController::class, 'get'])->name('api.product_attribute.get');
    });
});
