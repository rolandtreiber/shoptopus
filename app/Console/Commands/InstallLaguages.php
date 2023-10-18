<?php

namespace App\Console\Commands;

use App\Services\Remote\Translations\TranslationService;
use Google\Cloud\Core\Exception\GoogleException;
use Illuminate\Console\Command;
use Google\Cloud\Translate\V2\TranslateClient;

class InstallLaguages extends Command
{
    private TranslationService $translationService;
    public function __construct(
        TranslationService $translationService
    ) {
        parent::__construct();
        $this->translationService = $translationService;
    }
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:install-laguages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     * @throws GoogleException
     */
    public function handle()
    {
        $translationResult = $this->translationService->translate('This is a beautiful blue hat.', ['de', 'fr', 'es']);
        dd($translationResult);

//        $translate = new TranslateClient([
//            'key' => config('app.google_maps_api_key')
//        ]);

// Translate text from english to french.
//        $result = $translate->translate('Hello world!', [
//            'target' => 'de',
//        ]);
//
//        echo $result['text'] . "\n";

// Detect the language of a string.
//        $result = $translate->detectLanguage('Greetings from Michigan!');
//
//        echo $result['languageCode'] . "\n";

// Get the languages supported for translation specifically for your target language.
//        $languages = $translate->localizedLanguages([
//            'target' => 'en'
//        ]);
//
//        foreach ($languages as $language) {
//            echo $language['name'] . "\n";
//            echo $language['code'] . "\n";
//        }

// Get all languages supported for translation.
//        $languages = $translate->languages();
//
//        foreach ($languages as $language) {
//            echo $language . "\n";
//        }
        return 0;
    }
}
