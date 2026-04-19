<?php

namespace App\Services\AiAssistant;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GroqEntityAssistantService
{
    protected string $endpoint = 'https://api.groq.com/openai/v1/chat/completions';
    protected string $model = 'llama-3.3-70b-versatile';
    protected string $apiKey;

    public function __construct()
    {
        $this->apiKey = (string) env('GROQ_API_KEY', '');
    }

    public function ask(string $systemMessage, string $userMessage): ?string
    {
        if (blank($this->apiKey)) {
            Log::error('Groq API Key is missing for entity assistant.');
            return null;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->endpoint, [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => $systemMessage],
                    ['role' => 'user', 'content' => $userMessage],
                ],
                'temperature' => 0.4,
                'max_tokens' => 900,
            ]);

            if (! $response->successful()) {
                Log::error('Groq entity assistant request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return null;
            }

            $content = $response->json('choices.0.message.content');

            if (! is_string($content) || blank($content)) {
                return null;
            }

            return trim($content);
        } catch (\Throwable $e) {
            Log::error('Groq entity assistant exception', ['message' => $e->getMessage()]);

            return null;
        }
    }
}
