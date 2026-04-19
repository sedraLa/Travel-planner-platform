<?php

namespace App\Services\AiAssistant\Prompts;

use App\Models\Hotel;

class HotelPromptService
{
    public function systemMessage(): string
    {
        return 'You are a hotel travel advisor. Give practical, analytical, and personalized advice. Do not output JSON. Do not invent facts beyond provided data. If data is missing, acknowledge it and provide a reasonable general recommendation.';
    }

    public function userMessage(Hotel $hotel, string $question): string
    {
        $amenities = collect($hotel->amenities ?? [])->filter()->implode(', ');

        return "Hotel profile:\n"
            ."- Name: {$hotel->name}\n"
            ."- Location: {$hotel->city}, {$hotel->country}\n"
            ."- Destination: ".optional($hotel->destination)->name."\n"
            ."- Stars: ".($hotel->stars ?? 'N/A')."\n"
            ."- Price per night: ".($hotel->price_per_night ?? 'N/A')."\n"
            ."- Description: ".($hotel->description ?? 'N/A')."\n"
            ."- Amenities: ".($amenities ?: 'N/A')."\n"
            ."- Nearby landmarks: ".($hotel->nearby_landmarks ?? 'N/A')."\n"
            ."- Check-in/out: ".($hotel->check_in_time?->format('H:i') ?? 'N/A')." / ".($hotel->check_out_time?->format('H:i') ?? 'N/A')."\n"
            ."- Pets allowed: ".($hotel->pets_allowed ? 'Yes' : 'No')."\n"
            ."- Policies: ".($hotel->policies ?? 'N/A')."\n\n"
            ."User question: {$question}\n\n"
            .'Respond as a professional advisor in concise natural language with clear reasoning, pros/cons when relevant, and actionable tips.';
    }
}
