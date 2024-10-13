<?php

namespace App\Repositories\Admin\SystemService;

use App\Repositories\Admin\SystemService\SystemServiceRepositoryInterface;
use App\Services\Remote\Llm\LlmService;
use App\Services\Remote\Translations\TranslationService;

class SystemServiceRepository implements SystemServiceRepositoryInterface
{

    private TranslationService $translationService;
    private LlmService $llmService;

    public function __construct(
        TranslationService $translationService,
        LlmService $llmService
    )
    {
        $this->translationService = $translationService;
        $this->llmService = $llmService;
    }

    public function getTranslations($text, array $targetLanguages)
    {
        return $this->translationService->translate($text, $targetLanguages);
    }

    public function getOptimisedRewrite($text, array $targetLanguages)
    {
        $prompt = config('prompts.optimise-product-text-for-sales');
        $prompt = str_replace('[TARGET_LANGUAGES]', implode(", ", $targetLanguages), $prompt);
        $prompt = str_replace('[TEXT]', $text, $prompt);

        return $this->llmService->executePrompt($prompt);
    }
}
