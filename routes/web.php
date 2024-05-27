<?php

use App\Http\Controllers\BrowserTest\BrowserTestController;
use App\Http\Controllers\Common\InvoiceController;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// If the environment isn't local, the route shouldn't even exist let alone not wipe the database.
if (config('app.env') === 'local') {
    Route::get('/cypress-init', [BrowserTestController::class, 'cypressInit'])->name('cypress.init');
}

Route::get('/invoice/{token}', [InvoiceController::class, 'download'])->name('invoice.download');

Route::get('/admin/{argOne?}/{argTwo?}/{argThree?}/{argFour?}/{argFive?}', function () {
    return File::get(public_path() . '/admin/index.html');
});
