<?php

use App\Http\Controllers\Public\ProductController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:api', 'set.locale']], function () {
    Route::get('products', [ProductController::class, 'list'])->name('api.list.products');
});
