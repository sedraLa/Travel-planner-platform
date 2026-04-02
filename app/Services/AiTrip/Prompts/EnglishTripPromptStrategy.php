<?php

namespace App\Services\AiTrip\Prompts;

use App\Services\AiTrip\Contracts\TripPromptStrategy;

class EnglishTripPromptStrategy implements TripPromptStrategy
{
    public function language(): string
    {
        return 'en';
    }

    public function systemMessage(): string
    {
        return 'You are a trip planner that MUST only use the provided database catalog. Never invent destinations, hotels, or activities. Return valid JSON only.';
    }

    public function userMessage(array $tripData, array $catalog): string
    {
        $catalogJson = json_encode($catalog, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $selectedCategories = implode(', ', $tripData['categories'] ?? []);
        $selectedDestinations = implode(', ', array_map('strval', $tripData['destination_ids'] ?? []));

        return <<<PROMPT
Create a {$tripData['duration']}-day trip plan in English using only IDs and names that exist in the catalog below.

User input:
- Destination IDs (multi-select): {$selectedDestinations}
- Description: {$tripData['description']}
- Trip categories (multi-select): {$selectedCategories}
- Max participants: {$tripData['max_participants']}
- Budget: {$tripData['budget']}

STRICT RULES:
1) Use ONLY destination_id, hotel_id, and activity_id values from the catalog.
2) Do NOT generate trip packages data: includes, excludes, package highlights, package info, or transport assignments.
3) Do NOT generate meeting point fields (meeting_point_description / meeting_point_address); those are admin-managed via map/geocoding.
4) If a requested item does not exist, skip it and keep the plan realistic.
5) Prefer newest records (higher updated_at values in the catalog).
6) The number of days in "days" MUST equal exactly {$tripData['duration']}.
7) Day description should be rich and practical (transport rhythm, why this order, meal/family tips) and at least 35 words.
8) Hotel assignment rules:
   - If duration > 5 days, vary hotels across days when possible.
   - If activities look far apart by area/address, choose a closer hotel for that day.
   - If the itinerary includes multiple selected destinations, switch hotels to match destination flow.
9) Output must be JSON matching this shape exactly:
{
  "trip_name": "string",
  "trip_description": "string",
  "days": [
    {
      "day_number": 1,
      "title": "string",
      "description": "string",
      "hotel_id": 1,
      "activities": [
        {
          "activity_id": 1,
          "start_time": "09:00",
          "end_time": "11:00",
          "notes": "string"
        }
      ]
    }
  ],
  "markdown_summary": "string"
}

Catalog:
{$catalogJson}
PROMPT;
    }
}
