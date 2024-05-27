<?php

use App\Http\Controllers\Local\Rating\RatingController;
use Illuminate\Support\Facades\Route;

Route::group([
    'name' => 'api.'
], function () {
    Route::group([
        'name' => 'ratings.',
        'prefix' => 'ratings'
    ], function () {
        Route::get('/{product}', [RatingController::class, 'getAllForProduct'])->name('api.ratings.getAll');
    });
});
