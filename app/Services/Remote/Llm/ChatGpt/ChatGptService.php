<?php

namespace App\Services\Remote\Llm\ChatGpt;

use _PHPStan_690619d82\Nette\Neon\Exception;
use App\Services\Remote\Llm\LlmService;
use Illuminate\Support\Facades\Http;

class ChatGptService implements LlmService
{

    /**
     * @throws Exception
     */
    public function executePrompt(string $prompt, string $role = "You are an AI that strictly conforms to responses in JSON formatted strings. Your responses consist of valid JSON syntax, with no other comments, explainations, reasoninng, or dialogue not consisting of valid JSON.")
    {
        $apiKey = config('app.chatgpt-api-key');
        if (!$apiKey) {
            throw new Exception('No key specified.');
        }

        $response = Http::timeout(15)->withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json'
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => config('app.chatgpt-openapi-model'),
            'messages' => [
                [
                    "role" => "system",
                    "content" => $role
                ],
                [
                    "role" => "user",
                    "content" => $prompt
                ]
            ]
        ]);

        $res = $response->json();
        if (array_key_exists('choices', $res) && count($res['choices']) > 0) {
            // Find assistant message
            $assistantMessage = '';
            foreach ($res['choices'] as $choice) {
                if (array_key_exists('message', $choice)) {
                    if ($choice['message']['role'] === "assistant") {
                        $assistantMessage = json_decode($choice['message']['content'], true);
                    }
                }
            }
            return $assistantMessage;
        }

    }
}
