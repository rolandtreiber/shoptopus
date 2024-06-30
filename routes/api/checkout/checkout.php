<?php

use App\Http\Controllers\Local\Cart\CartController;
use App\Http\Controllers\Local\Checkout\CheckoutController;
use Illuminate\Support\Facades\Route;

Route::group([
    'name' => 'api.'
], function () {
    Route::group([
        'name' => 'checkout.',
        'prefix' => 'checkout'
    ], function () {
        Route::post('/get-available-delivery-types', [CheckoutController::class, 'getAvailableDeliveryTypes'])->name('api.checkout.get.available-delivery-types');
        Route::post('/create-pending-order', [CheckoutController::class, 'createPendingOrderFromCart'])->name('api.checkout.create.pending-order');
        Route::post('/revert-order', [CheckoutController::class, 'revertOrder'])->name('api.checkout.revert.order');
    });
});
