<?php

use App\Http\Controllers\Address\AddressController;
use Illuminate\Support\Facades\Route;

Route::group([
    'name' => 'api.',
], function () {
    Route::prefix('addresses')->middleware('auth:api')->group([
        'name' => 'addresses.', ], function () {
            Route::get('/', [AddressController::class, 'getAll'])->name('api.addresses.getAll');
            Route::post('/', [AddressController::class, 'post'])->name('api.addresses.create');
        });

    Route::prefix('address')->middleware('auth:api')->group([
        'name' => 'address.', ], function () {
            Route::get('/{id}', [AddressController::class, 'get'])->name('api.address.get');
            Route::patch('/{id}', [AddressController::class, 'update'])->name('api.address.update');
            Route::delete('/{id}', [AddressController::class, 'delete'])->name('api.address.delete');
        });
});
