<?php

use App\Http\Controllers\Common\AppController;
use App\Http\Controllers\Common\NotificationController;
use App\Http\Controllers\Local\HomePage\HomePageController;
use App\Http\Controllers\Local\Order\OrderController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api', 'set.locale')->group(function () {
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/latest', [NotificationController::class, 'latest']);
        Route::patch('clear', [NotificationController::class, 'clear']);
        Route::get('/{notification}', [NotificationController::class, 'show']);
    });
});

// App meta information
Route::get('home', [HomePageController::class, 'index'])->name('admin.api.get-home-page-data');
Route::get('meta', [AppController::class, 'getMetaInformation'])->name('admin.api.get-meta-information');
Route::get('shared-options', [AppController::class, 'getSharedOptions'])->name('admin.api.get-shared-options');

Route::get('download-paid-file/{paidFileContent}', [OrderController::class, 'downloadPaidFile'])->name('public.api.download-paid-file');
