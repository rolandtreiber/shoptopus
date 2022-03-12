<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VoucherCode\VoucherCodeController;

Route::group([
    'name' => 'api.',
    'middleware' => 'api'
], function () {
    Route::group([
        'name' => 'voucher_codes.',
        'prefix' => 'voucher_codes'
    ], function () {
        Route::get('/', [VoucherCodeController::class, 'getAll'])->name('api.voucher_codes.getAll');
        Route::get('/{id}', [VoucherCodeController::class, 'get'])->name('api.voucher_codes.get');
        Route::post('/', [VoucherCodeController::class, 'post'])->name('api.voucher_codes.create');
        Route::patch('/{id}', [VoucherCodeController::class, 'update'])->name('api.voucher_codes.update');
        Route::delete('/{id}',  [VoucherCodeController::class, 'delete'])->name('api.voucher_codes.delete');
    });
});
