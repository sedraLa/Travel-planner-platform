<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GroqTripPlannerService
{
    protected $apiKey;
    protected $endpoint = 'https://api.groq.com/openai/v1/chat/completions';
    protected $model = 'llama-3.3-70b-versatile';

    public function __construct()
    {
        $this->apiKey = env('GROQ_API_KEY');
    }

    /**
     * Generates a trip plan using Groq API.
     *
     * @param array $tripData
     * @param string $language 'en' or 'ar'
     * @return string|null The generated trip plan text or null on failure.
     */
    public function generateTripPlan(array $tripData, string $language = 'en'): ?string
    {
        if (!$this->apiKey) {
            Log::error('Groq API Key is missing.');
            return null;
        }

        // Build the prompt based on the required language
        if ($language === 'ar') {
            $prompt = $this->buildArabicPrompt($tripData);
            $systemMessage = 'أنت مخطط رحلات سياحية محترف. مهمتك هي إنشاء خطط رحلات مفصلة ومنظمة بشكل جيد باللغة العربية.';
        } else {
            $prompt = $this->buildEnglishPrompt($tripData);
            $systemMessage = 'You are a professional travel planner. Your task is to create detailed and well-organized trip itineraries in English.';
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->endpoint, [
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $systemMessage
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.7,
                'max_tokens' => 3000,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['choices'][0]['message']['content'] ?? null;
            } else {
                Log::error('Groq API Request Failed', ['status' => $response->status(), 'body' => $response->body()]);
                return null;
            }

        } catch (\Exception $e) {
            Log::error('Groq API Exception', ['message' => $e->getMessage()]);
            return null;
        }
    }

    protected function buildEnglishPrompt(array $data): string
    {
        return "I want to plan a trip. Please create a detailed and well-organized trip itinerary in Markdown format.
        - Trip Description: {$data['description']}
        - Duration: {$data['duration']} days
        - Number of Travelers: {$data['travelers_number']}
        - Estimated Budget: " . ($data['budget'] ? "\${$data['budget']}" : "Not specified") . "
        
        **Requirements:**
        1. The plan must be divided by day (Day 1, Day 2, etc.).
        2. For each day, specify suggested activities (Morning, Afternoon, Evening).
        3. The response must be in clear, professional English.
        4. The response must be in clean, readable Markdown format (use headings and lists).";
    }

    protected function buildArabicPrompt(array $data): string
    {
        return "أنا أرغب في تخطيط رحلة. الرجاء إنشاء خطة رحلة مفصلة ومنظمة بتنسيق Markdown.
        - وصف الرحلة: {$data['description']}
        - عدد الأيام: {$data['duration']} أيام
        - عدد المسافرين: {$data['travelers_number']}
        - الميزانية التقديرية: " . ($data['budget'] ? "{$data['budget']}$" : "غير محددة") . "
        
        **المتطلبات:**
        1. يجب أن تكون الخطة مقسمة حسب الأيام (اليوم الأول، اليوم الثاني، إلخ).
        2. لكل يوم، يجب تحديد الأنشطة المقترحة (صباحاً، ظهراً، مساءً).
        3. يجب أن يكون الرد باللغة العربية الفصحى فقط.
        4. يجب أن يكون الرد بتنسيق Markdown واضح وسهل القراءة (استخدمي العناوين والقوائم).";
    }
}
