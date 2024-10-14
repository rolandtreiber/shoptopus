<?php

use App\Http\Controllers\Local\Payment\PaymentController;
use Illuminate\Support\Facades\Route;

Route::group([
    'name' => 'api.'
], function () {
    Route::group([
        'name' => 'payment.',
        'prefix' => 'payment',
        'middleware' => 'api'
    ], function () {
        Route::get('/{provider}/settings', [PaymentController::class, 'getClientSettings'])->name('api.payment.get.settings.public');
    });

    Route::group([
        'name' => 'payment.',
        'prefix' => 'payment',
        'middleware' => 'api'
    ], function () {
        Route::post('/execute', [PaymentController::class, 'execute'])->name('api.payment.execute');
    });
});
