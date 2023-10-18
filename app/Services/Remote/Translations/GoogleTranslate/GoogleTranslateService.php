<?php

namespace App\Services\Remote\Translations\GoogleTranslate;

use App\Services\Remote\Translations\TranslationService;
use Google\Cloud\Core\Exception\GoogleException;
use Google\Cloud\Translate\V2\TranslateClient;

class GoogleTranslateService implements TranslationService
{
    /**
     * @throws GoogleException
     */
    public function translate($text, array $languages): array
    {
        $translate = new TranslateClient([
            'key' => config('app.google_maps_api_key')
        ]);

        $result = [];

        foreach ($languages as $language) {
            $translationResult = $translate->translate($text, [
                'target' => $language,
            ]);
            $result[$language] = $translationResult['text'];
        }
        return $result;
    }
}
