<?php

namespace App\Services;

use App\Services\AiTrip\Contracts\TripPromptStrategy;
use App\Services\AiTrip\Prompts\ArabicTripPromptStrategy;
use App\Services\AiTrip\Prompts\EnglishTripPromptStrategy;
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

    public function __construct(protected TripCatalogService $catalogService)
    {
        $this->apiKey = (string) env('GROQ_API_KEY', '');

        $strategies = [
            new EnglishTripPromptStrategy(),
            new ArabicTripPromptStrategy(),
        ];

        $this->promptStrategies = collect($strategies)
            ->keyBy(fn (TripPromptStrategy $strategy) => $strategy->language())
            ->all();
    }

    public function generateTripPlan(array $tripData, string $language = 'en'): ?array
    {
        if (blank($this->apiKey)) {
            Log::error('Groq API Key is missing.');
            return null;
        }

        $strategy = $this->promptStrategies[$language] ?? $this->promptStrategies['en'];
        $catalog = $this->catalogService->buildCatalog(
            $tripData['destination_ids'] ?? [],
            $tripData['categories'] ?? []
        );

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->endpoint, [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => $strategy->systemMessage()],
                    ['role' => 'user', 'content' => $strategy->userMessage($tripData, $catalog)],
                ],
                'temperature' => 0.2,
                'max_tokens' => 3500,
                'response_format' => ['type' => 'json_object'],
            ]);

            if (! $response->successful()) {
                Log::error('Groq API Request Failed', ['status' => $response->status(), 'body' => $response->body()]);
                return null;
            }

            $content = $response->json('choices.0.message.content');

            if (! is_string($content) || blank($content)) {
                return null;
            }

            $decoded = json_decode($content, true);

            if (! is_array($decoded)) {
                return null;
            }

            return $this->sanitizeAgainstCatalog($decoded, $catalog, (int) ($tripData['duration'] ?? 1));
        } catch (\Throwable $e) {
            Log::error('Groq API Exception', ['message' => $e->getMessage()]);
            return null;
        }
    }

    //safety against AI
    protected function sanitizeAgainstCatalog(array $plan, array $catalog, int $requestedDuration): array
    {
        $allowedHotelIds = collect($catalog['hotels'])->pluck('id')->map(fn ($id) => (int) $id)->all();
        $allowedActivityIds = collect($catalog['activities'])->pluck('id')->map(fn ($id) => (int) $id)->all();

        $sanitizedDays = collect($plan['days'] ?? [])
            ->map(function ($day, $index) use ($allowedHotelIds, $allowedActivityIds) {
                $activities = collect($day['activities'] ?? [])
                    ->filter(fn ($activity) => in_array((int) ($activity['activity_id'] ?? 0), $allowedActivityIds, true))
                    ->map(fn ($activity) => [
                        'activity_id' => (int) $activity['activity_id'],
                        'start_time' => $activity['start_time'] ?? null,
                        'end_time' => $activity['end_time'] ?? null,
                        'notes' => $activity['notes'] ?? null,
                    ])
                    ->values()
                    ->all();

                return [
                    'day_number' => (int) ($day['day_number'] ?? ($index + 1)),
                    'title' => (string) ($day['title'] ?? 'Day ' . ($index + 1)),
                    'description' => (string) ($day['description'] ?? ''),
                    'hotel_id' => in_array((int) ($day['hotel_id'] ?? 0), $allowedHotelIds, true)
                        ? (int) $day['hotel_id']
                        : null,
                    'activities' => $activities,
                ];
            })
            ->values()
            ->all();

        $requestedDuration = max(1, $requestedDuration);
        $plan['days'] = collect(range(1, $requestedDuration))
            ->map(function (int $dayNumber) use ($sanitizedDays) {
                $existing = $sanitizedDays[$dayNumber - 1] ?? null;

                if ($existing) {
                    $existing['day_number'] = $dayNumber;
                    return $existing;
                }

                return [
                    'day_number' => $dayNumber,
                    'title' => 'Day ' . $dayNumber,
                    'description' => '',
                    'hotel_id' => null,
                    'activities' => [],
                ];
            })
            ->all();

        $plan['days'] = $this->optimizeHotelsAcrossDays($plan['days'], $catalog['hotels'] ?? [], $requestedDuration);

        $plan['trip_name'] = (string) ($plan['trip_name'] ?? 'AI Generated Trip');
        $plan['trip_description'] = (string) ($plan['trip_description'] ?? 'A detailed trip crafted with curated local experiences, smart pacing, and practical stay recommendations from your platform catalog.');
        $plan['markdown_summary'] = (string) ($plan['markdown_summary'] ?? '');

        return $plan;
    }

    protected function optimizeHotelsAcrossDays(array $days, array $hotels, int $duration): array
    {
        if (empty($days) || empty($hotels)) {
            return $days;
        }

        $hotelIds = collect($hotels)->pluck('id')->map(fn ($id) => (int) $id)->values()->all();
        $activeHotelIndex = 0;

        foreach ($days as $index => $day) {
            if (! empty($day['hotel_id']) && in_array((int) $day['hotel_id'], $hotelIds, true)) {
                $currentIndex = array_search((int) $day['hotel_id'], $hotelIds, true);
                $activeHotelIndex = $currentIndex === false ? $activeHotelIndex : $currentIndex;
                continue;
            }

            // For long trips (>5 days), rotate hotel every 3 days for better comfort and location fit.
            if ($duration > 5 && $index > 0 && $index % 3 === 0) {
                $activeHotelIndex = ($activeHotelIndex + 1) % count($hotelIds);
            }

            $days[$index]['hotel_id'] = $hotelIds[$activeHotelIndex];
        }

        return $days;
    }
}
