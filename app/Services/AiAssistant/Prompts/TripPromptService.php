<?php

namespace App\Services\AiAssistant\Prompts;

use App\Models\Trip;

class TripPromptService
{
    public function systemMessage(): string
    {
        return 'You are a trip decision assistant. Help travelers understand whether this trip fits their goals, budget style, and constraints. Provide analytical and personalized recommendations in natural language only.';
    }

    public function userMessage(Trip $trip, string $question): string
    {
        $destinations = $trip->itineraryDestinations->pluck('name')->filter()->implode(', ');
        $packageSummaries = $trip->packages
            ->take(5)
            ->map(fn ($package) => $package->name.' ($'.number_format((float) $package->price, 2).' per person)')
            ->implode('; ');

        return "Trip profile:\n"
            ."- Name: {$trip->name}\n"
            ."- Category: ".($trip->category ?? 'N/A')."\n"
            ."- Duration days: ".($trip->duration_days ?? 'N/A')."\n"
            ."- Description: ".($trip->description ?? 'N/A')."\n"
            ."- Max participants: ".($trip->max_participants ?? 'N/A')."\n"
            ."- Destinations in itinerary: ".($destinations ?: 'N/A')."\n"
            ."- Meeting point: ".($trip->meeting_point_description ?? 'N/A')."\n"
            ."- Meeting address: ".($trip->meeting_point_address ?? 'N/A')."\n"
            ."- Packages: ".($packageSummaries ?: 'N/A')."\n\n"
            ."User question: {$question}\n\n"
            .'Give helpful guidance with clear trade-offs and practical next steps.';
    }
}
