<?php

namespace App\Services\Remote\Translations;

interface TranslationService
{
    public function translate($text, array $languages): array;
}
