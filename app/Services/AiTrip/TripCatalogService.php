<?php

namespace App\Services\AiTrip;

use App\Enums\Category;
use App\Models\Destination;

class TripCatalogService
{
    public function buildCatalog(array $destinationIds, array $tripCategories = []): array
    {
        //cleaning
        $destinationIds = collect($destinationIds)->map(fn ($id) => (int) $id)->filter()->unique()->values();
        $normalizedCategories = $this->normalizeCategories($tripCategories);

        //get data 
        $destinations = Destination::query()
            ->whereIn('id', $destinationIds)
            ->with([
                'hotels' => fn ($query) => $query->orderByDesc('stars')->orderBy('price_per_night')->orderByDesc('updated_at')->limit(30),
                'activities' => fn ($query) => $query
                    ->where('availability', true)
                    ->when(! empty($normalizedCategories), fn ($subQuery) => $subQuery->whereIn('category', $normalizedCategories))
                    ->orderBy('price')
                    ->orderBy('duration')
                    ->orderByDesc('updated_at')
                    ->limit(60),
            ])
            ->get();

            //data sent to api
        return [
            'destinations' => $destinations->map(fn ($destination) => [
                'id' => $destination->id,
                'name' => $destination->name,
                'city' => $destination->city,
                'country' => $destination->country,
                'updated_at' => optional($destination->updated_at)?->toDateTimeString(),
            ])->values()->all(),

            //to ensure the plan stays relevant
            'filters' => [
                'requested_destination_ids' => $destinationIds->all(),
                'requested_trip_categories' => $tripCategories,
                'normalized_activity_categories' => $normalizedCategories,
            ],
            'hotels' => $destinations->flatMap(fn ($destination) => $destination->hotels->map(fn ($hotel) => [ //one array to all hotels
                'id' => $hotel->id,
                'destination_id' => $destination->id,
                'destination_name' => $destination->name,
                'name' => $hotel->name,
                'city' => $hotel->city,
                'country' => $hotel->country,
                'description' => $hotel->description,
                'price_per_night' => $hotel->price_per_night,
                'stars' => $hotel->stars,
                'amenities' => $hotel->amenities ?? [],
                'pets_allowed' => $hotel->pets_allowed,
                'check_in_time' => $hotel->check_in_time?->format('H:i'),
                'check_out_time' => $hotel->check_out_time?->format('H:i'),
                'policies' => $hotel->policies,
                'nearby_landmarks' => $hotel->nearby_landmarks,
                'updated_at' => optional($hotel->updated_at)?->toDateTimeString(),
            ]))->values()->all(),
            'activities' => $destinations->flatMap(fn ($destination) => $destination->activities->map(fn ($activity) => [
                'id' => $activity->id,
                'destination_id' => $destination->id,
                'destination_name' => $destination->name,
                'name' => $activity->name,
                'description' => $activity->description,
                'address' => $activity->address,
                'price' => $activity->price,
                'category' => $activity->category,
                'duration' => $activity->duration,
                'duration_unit' => $activity->duration_unit,
                'amenities' => $activity->amenities ?? [],
                'highlights' => $activity->highlights,
                'updated_at' => optional($activity->updated_at)?->toDateTimeString(),
            ]))->values()->all(),
        ];
    }

    protected function normalizeCategories(array $categories): array
    {
        $allowed = Category::values();
        return collect($categories)
            ->map(fn ($category) => strtolower(trim((string) $category)))
            ->map(fn (string $category) => str_replace(' ', '_', $category))
            ->filter(fn (string $category) => in_array($category, $allowed, true)) //filter according to enum
            ->unique()
            ->values()
            ->all();
    }
}
