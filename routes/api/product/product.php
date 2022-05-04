<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Product\ProductController;

Route::group([
    'name' => 'api.',
    'middleware' => 'api'
], function () {
    Route::group([
        'name' => 'products.',
        'prefix' => 'products'
    ], function () {
        Route::get('/', [ProductController::class, 'getAll'])->name('api.products.getAll');
        Route::get('/{id}', [ProductController::class, 'get'])->name('api.products.get');
        Route::get('/slug/{slug}', [ProductController::class, 'getBySlug'])->name('api.products.getBySlug');
    });
});
