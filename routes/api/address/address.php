<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Address\AddressController;

Route::group([
    'name' => 'api.'
], function () {
    Route::group([
        'name' => 'addresses.',
        'prefix' => 'addresses',
        'middleware' => 'auth:api'
    ], function () {
        Route::get('/', [AddressController::class, 'getAll'])->name('api.addresses.getAll');
        Route::post('/', [AddressController::class, 'post'])->name('api.addresses.create');
    });

    Route::group([
        'name' => 'address.',
        'prefix' => 'address',
        'middleware' => 'auth:api'
    ], function () {
        Route::get('/{id}', [AddressController::class, 'get'])->name('api.address.get');
        Route::patch('/{id}', [AddressController::class, 'update'])->name('api.address.update');
        Route::delete('/{id}',  [AddressController::class, 'delete'])->name('api.address.delete');
    });
});
