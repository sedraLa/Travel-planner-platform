<?php

namespace App\Services;

use App\Services\AiTrip\Contracts\TripPromptStrategy;
use App\Services\AiTrip\Prompts\ArabicTripPromptStrategy;
use App\Services\AiTrip\Prompts\EnglishTripPromptStrategy;
use App\Services\AiTrip\Sanitizers\TripPlanSanitizer;
use App\Services\AiTrip\TripCatalogService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GroqTripPlannerService
{
    protected string $apiKey;
    protected string $endpoint = 'https://api.groq.com/openai/v1/chat/completions';
    protected string $model = 'llama-3.3-70b-versatile';

    /** @var array<string, TripPromptStrategy> */
    protected array $promptStrategies;

    public function __construct(
        protected TripCatalogService $catalogService,
        protected TripPlanSanitizer $tripPlanSanitizer
    ) {
        $this->apiKey = (string) env('GROQ_API_KEY', '');

        $strategies = [
            new EnglishTripPromptStrategy(),
            new ArabicTripPromptStrategy(),
        ];

        $this->promptStrategies = collect($strategies)
            ->keyBy(fn (TripPromptStrategy $strategy) => $strategy->language()) //use language as key
            ->all();
    }

    public function generateTripPlan(array $tripData, string $language = 'en'): ?array
    {
        //check api key
        if (blank($this->apiKey)) {
            Log::error('Groq API Key is missing.');
            return null;
        }

        //language
        $strategy = $this->promptStrategies[$language] ?? $this->promptStrategies['en'];

        //catalog building
        $catalog = $this->catalogService->buildCatalog(
            $tripData['destination_ids'] ?? [],
            $tripData['categories'] ?? []
        );

        //send request
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->endpoint, [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => $strategy->systemMessage()], //instructions AI personality
                    ['role' => 'user', 'content' => $strategy->userMessage($tripData, $catalog)], //request
                ],
                'temperature' => 0.2,
                'max_tokens' => 3500,
                'response_format' => ['type' => 'json_object'],
            ]);

            if (! $response->successful()) {
                Log::error('Groq API Request Failed', ['status' => $response->status(), 'body' => $response->body()]);
                return null;
            }

            //extract output
            $content = $response->json('choices.0.message.content');

            if (! is_string($content) || blank($content)) {
                return null;
            }

            //convert to array
            $decoded = json_decode($content, true);

            if (! is_array($decoded)) {
                return null;
            }

            //validate respond data
            return $this->tripPlanSanitizer->sanitizeAgainstCatalog($decoded, $catalog, (int) ($tripData['duration'] ?? 1));
        } catch (\Throwable $e) {
            Log::error('Groq API Exception', ['message' => $e->getMessage()]);
            return null;
        }
    }

}
