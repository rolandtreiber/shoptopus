<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DeliveryType\DeliveryTypeController;

Route::group([
    'name' => 'api.',
    'middleware' => 'api'
], function () {
    Route::group([
        'name' => 'delivery_types.',
        'prefix' => 'delivery_types'
    ], function () {
        Route::get('/', [DeliveryTypeController::class, 'getAll'])->name('api.delivery_types.getAll');
        Route::get('/{id}', [DeliveryTypeController::class, 'get'])->name('api.delivery_types.get');
        Route::post('/', [DeliveryTypeController::class, 'post'])->name('api.delivery_types.create');
        Route::patch('/{id}', [DeliveryTypeController::class, 'update'])->name('api.delivery_types.update');
        Route::delete('/{id}',  [DeliveryTypeController::class, 'delete'])->name('api.delivery_types.delete');
    });
});
