<?php

namespace App\Providers;

use App\Services\Remote\Translations\GoogleTranslate\GoogleTranslateService;
use App\Services\Remote\Translations\TranslationService;
use Illuminate\Support\ServiceProvider;

class TranslationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(TranslationService::class, GoogleTranslateService::class);
    }
}
