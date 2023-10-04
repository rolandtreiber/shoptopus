<?php

use App\Http\Controllers\Common\AppController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Order\OrderController;
use App\Http\Controllers\Public\ProductController;
use App\Models\PaidFileContent;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api', 'set.locale')->group(function () {
    Route::get('list-products', [ProductController::class, 'list'])->name('api.list.products');

    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/latest', [NotificationController::class, 'latest']);
        Route::patch('clear', [NotificationController::class, 'clear']);
        Route::get('/{notification}', [NotificationController::class, 'show']);
    });
});

// App meta information
Route::get('meta', [AppController::class, 'getMetaInformation'])->name('admin.api.get-meta-information');
Route::get('shared-options', [AppController::class, 'getSharedOptions'])->name('admin.api.get-shared-options');

Route::get('download-paid-file/{paidFileContent}', [OrderController::class, 'downloadPaidFile'])->name('public.api.download-paid-file');
