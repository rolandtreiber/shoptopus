<?php

use App\Http\Controllers\Payment\PaymentController;
use Illuminate\Support\Facades\Route;

Route::group([
    'name' => 'api.',
], function () {
    Route::prefix('payment')->middleware('api')->group([
        'name' => 'payment.',], function () {
        Route::get('/{provider}/settings', [PaymentController::class, 'getClientSettings'])->name('api.payment.get.settings.public');
    });

    Route::prefix('payment')->middleware('auth:api')->group([
        'name' => 'payment.',], function () {
        Route::post('/execute', [PaymentController::class, 'execute'])->name('api.payment.execute');
    });
});
