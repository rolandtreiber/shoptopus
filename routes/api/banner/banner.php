<?php

use App\Http\Controllers\Local\Banner\BannerController;
use Illuminate\Support\Facades\Route;

Route::group([
    'name' => 'api.'
], function () {
    Route::group([
        'name' => 'banners.',
        'prefix' => 'banners'
    ], function () {
        Route::get('/', [BannerController::class, 'getAll'])->name('api.banners.getAll');
        Route::post('/{banner}/click', [BannerController::class, 'clicked'])->name('api.banner.clicked');
    });
});
