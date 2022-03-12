<?php

namespace Shoptopus\ExcelImportExport;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class ExcelImportExportServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/excel_import_export.php' => config_path('excel_import_export.php')
        ]);
        $this->registerResources();
    }

    public function registerResources()
    {
        $this->registerRoutes();
    }

    public function register()
    {
        $this->app->singleton(ExcelImportExportInterface::class, function () {
            return new ExcelImportExport();
        });
    }

    protected function registerRoutes()
    {
        Route::group([], function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        });
    }
}
