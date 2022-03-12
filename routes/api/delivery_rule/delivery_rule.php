<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DeliveryRule\DeliveryRuleController;

Route::group([
    'name' => 'api.',
    'middleware' => 'api'
], function () {
    Route::group([
        'name' => 'delivery_rules.',
        'prefix' => 'delivery_rules'
    ], function () {
        Route::get('/', [DeliveryRuleController::class, 'getAll'])->name('api.delivery_rules.getAll');
        Route::get('/{id}', [DeliveryRuleController::class, 'get'])->name('api.delivery_rules.get');
        Route::post('/', [DeliveryRuleController::class, 'post'])->name('api.delivery_rules.create');
        Route::patch('/{id}', [DeliveryRuleController::class, 'update'])->name('api.delivery_rules.update');
        Route::delete('/{id}',  [DeliveryRuleController::class, 'delete'])->name('api.delivery_rules.delete');
    });
});
