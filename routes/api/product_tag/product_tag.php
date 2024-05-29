<?php

use App\Http\Controllers\Local\ProductTag\ProductTagController;
use Illuminate\Support\Facades\Route;

Route::group([
    'name' => 'api.'
], function () {
    Route::group([
        'name' => 'product_tags.',
        'prefix' => 'product_tags'
    ], function () {
        Route::get('/', [ProductTagController::class, 'getAll'])->name('api.product_tags.getAll');
    });

    Route::group([
        'name' => 'product_tag.',
        'prefix' => 'product_tag'
    ], function () {
        Route::get('/{id}', [ProductTagController::class, 'get'])->name('api.product_tag.get');
    });
});
