<?php

namespace App\Services\AiAssistant\Prompts;

use App\Models\Destination;

class DestinationPromptService
{
    public function systemMessage(): string
    {
        return 'You are a destination travel advisor. Give insightful itineraries, practical tips, safety, culture, and budget guidance using only supplied context plus common travel best practices. No JSON output.';
    }

    public function userMessage(Destination $destination, string $question): string
    {
        $highlights = $destination->highlights->pluck('title')->filter()->take(10)->implode(', ');

        return "Destination profile:\n"
            ."- Name: {$destination->name}\n"
            ."- City/Country: {$destination->city}, {$destination->country}\n"
            ."- Description: ".($destination->description ?? 'N/A')."\n"
            ."- Best time to visit: ".($destination->best_time_to_visit ?? 'N/A')."\n"
            ."- Language: ".($destination->language ?? 'N/A')."\n"
            ."- Currency: ".($destination->currency ?? 'N/A')."\n"
            ."- Nearest airport: ".($destination->nearest_airport ?? 'N/A')."\n"
            ."- Emergency numbers: ".($destination->emergency_numbers ?? 'N/A')."\n"
            ."- Local tip: ".($destination->local_tip ?? 'N/A')."\n"
            ."- Known highlights: ".($highlights ?: 'N/A')."\n\n"
            ."User question: {$question}\n\n"
            .'Give a tailored answer with short sections when helpful (e.g., what to do, planning logic, cautions, money-saving moves).';
    }
}
