<?php

namespace App\Services\AiAssistant\Prompts;

use App\Models\Activity;

class ActivityPromptService
{
    public function systemMessage(): string
    {
        return 'You are an activity planning assistant. Explain timing, preparation, difficulty, safety, and suitability clearly. Be honest about unknowns. Do not output JSON.';
    }

    public function userMessage(Activity $activity, string $question): string
    {
        $amenities = collect($activity->amenities ?? [])->filter()->implode(', ');
        $highlights = $activity->highlights->pluck('title')->filter()->implode(', ');

        return "Activity profile:\n"
            ."- Name: {$activity->name}\n"
            ."- Destination: ".optional($activity->destination)->name."\n"
            ."- Category: ".($activity->category ?? 'N/A')."\n"
            ."- Description: ".($activity->description ?? 'N/A')."\n"
            ."- Duration: ".($activity->duration ?? 'N/A').' '.($activity->duration_unit ?? '')."\n"
            ."- Price: ".($activity->price ?? 'N/A')."\n"
            ."- Difficulty level: ".($activity->difficulty_level ?? 'N/A')."\n"
            ."- Requirements: ".($activity->requirements ?? 'N/A')."\n"
            ."- Amenities: ".($amenities ?: 'N/A')."\n"
            ."- Highlights: ".($highlights ?: 'N/A')."\n"
            ."- Family friendly: ".($activity->family_friendly ? 'Yes' : 'No')."\n"
            ."- Pets allowed: ".($activity->pets_allowed ? 'Yes' : 'No')."\n"
            ."- Requires booking: ".($activity->requires_booking ? 'Yes' : 'No')."\n\n"
            ."User question: {$question}\n\n"
            .'Respond with practical recommendations and brief preparation checklist when useful.';
    }
}
