<?php

namespace App\Services\AiTrip;

use App\Models\Destination;

class TripCatalogService
{
    public function buildCatalog(int $destinationId, ?string $tripCategory = null): array
    {
        $normalizedCategory = $this->normalizeCategory($tripCategory);

        $destination = Destination::query()->with([
            'hotels' => fn ($query) => $query->orderByDesc('updated_at')->limit(20),
            'activities' => fn ($query) => $query
                ->where('is_active', true)
                ->when($normalizedCategory !== null, fn ($subQuery) => $subQuery->where('category', $normalizedCategory))
                ->orderBy('price')
                ->orderBy('duration')
                ->orderByDesc('updated_at')
                ->limit(40),
        ])->findOrFail($destinationId);

        return [
            'destination' => [
                'id' => $destination->id,
                'name' => $destination->name,
                'city' => $destination->city,
                'country' => $destination->country,
                'updated_at' => optional($destination->updated_at)?->toDateTimeString(),
            ],
            'filters' => [
                'requested_trip_category' => $tripCategory,
                'normalized_activity_category' => $normalizedCategory,
            ],
            'hotels' => $destination->hotels->map(fn ($hotel) => [
                'id' => $hotel->id,
                'name' => $hotel->name,
                'price_per_night' => $hotel->price_per_night,
                'stars' => $hotel->stars,
                'updated_at' => optional($hotel->updated_at)?->toDateTimeString(),
            ])->values()->all(),
            'activities' => $destination->activities->map(fn ($activity) => [
                'id' => $activity->id,
                'name' => $activity->name,
                'price' => $activity->price,
                'category' => $activity->category,
                'duration' => $activity->duration,
                'duration_unit' => $activity->duration_unit,
                'updated_at' => optional($activity->updated_at)?->toDateTimeString(),
            ])->values()->all(),
        ];
    }

    protected function normalizeCategory(?string $category): ?string
    {
        $normalized = strtolower(trim((string) $category));
        $normalized = str_replace(' ', '_', $normalized);

        return in_array($normalized, ['culture', 'nature', 'shopping', 'sports', 'entertainment'], true)
            ? $normalized
            : null;
    }
}
