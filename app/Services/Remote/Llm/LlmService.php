<?php

namespace App\Services\Remote\Llm;

interface LlmService
{
    public function executePrompt(string $prompt);
}
