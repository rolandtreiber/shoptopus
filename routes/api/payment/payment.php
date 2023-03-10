<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Payment\PaymentController;

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
        'middleware' => 'auth:api'
    ], function () {
        Route::post('/execute', [PaymentController::class, 'execute'])->name('api.payment.execute');
    });
});
