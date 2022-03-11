<?php

use Illuminate\Support\Facades\Route;
use Shoptopus\ExcelImportExport\Http\Controllers\ImportExportController;

Route::group(['prefix' => config('excel_import_export.prefix'), 'middleware' => config('excel_import_export.middleware') ? config('excel_import_export.middleware') : null], function () {
    Route::get('export', [ImportExportController::class, 'export']);
    Route::get('template', [ImportExportController::class, 'template']);
});
