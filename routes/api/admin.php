<?php

use App\Http\Controllers\Admin\ProductController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:api', 'admin', 'set.locale']], function () {
    Route::group(['prefix' => 'admin'], function () {
        Route::get('products', [ProductController::class, 'list'])->name('admin.api.list.products');
        Route::group(['prefix' => 'product'], function () {
            Route::get('/{id}', [ProductController::class, 'show'])->name('admin.api.show.product');
        });
    });
});
