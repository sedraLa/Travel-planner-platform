<?php

namespace App\Services\AiTrip;

use App\Models\Trip;
use App\Models\TripExclude;
use App\Models\TripHighlight;
use App\Models\TripInclude;
use App\Models\TripPackage;
use App\Models\TripPackageHotel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TripPackagesService
{
    public function savePackages(Trip $trip, array $payload): void
    {
        DB::transaction(function () use ($trip, $payload) {
            $keepPackageIds = [];
            $canonicalHotelIds = $this->canonicalHotelIdsFromDays($trip);

            foreach (($payload['packages'] ?? []) as $packagePayload) {
                if (blank($packagePayload['name'] ?? null) && blank($packagePayload['price'] ?? null)) {
                    continue;
                }

                $package = TripPackage::query()->updateOrCreate(
                    [
                        'id' => $packagePayload['id'] ?? null,
                        'trip_id' => $trip->id,
                    ],
                    [
                        'name' => $packagePayload['name'] ?? 'Package',
                        'price' => $packagePayload['price'] ?? 0,
                    ]
                );

                $keepPackageIds[] = $package->id;
                $this->syncPackageTextBlocks($package, $packagePayload);
                $this->syncPackageHotelsFromDays($package, $packagePayload['hotels'] ?? [], $canonicalHotelIds);
            }

            $trip->packages()->whereNotIn('id', $keepPackageIds ?: [0])->delete();
        });
    }

    public function syncPackageHotelsFromDays(TripPackage $package, array $hotelPayloads, array $canonicalHotelIds): void
    {
        $payloadByHotelId = collect($hotelPayloads)
            ->filter(fn ($hotelPayload) => ! empty($hotelPayload['hotel_id']))
            ->keyBy(fn ($hotelPayload) => (int) $hotelPayload['hotel_id']);

        $payloadByIndex = collect($hotelPayloads)->values();

        $package->packageHotels()->delete();

        foreach ($canonicalHotelIds as $index => $hotelId) {
            $matchedPayload = $payloadByHotelId->get($hotelId) ?? $payloadByIndex->get($index, []);

            TripPackageHotel::create([
                'trip_package_id' => $package->id,
                'hotel_id' => $hotelId,
                'room_type' => $matchedPayload['room_type'] ?? null,
                'meal_plan' => $matchedPayload['meal_plan'] ?? null,
                'amenities' => collect(explode(',', (string) ($matchedPayload['amenities'] ?? '')))
                    ->map(fn ($item) => trim($item))
                    ->filter()
                    ->values()
                    ->all(),
                'notes' => $matchedPayload['notes'] ?? null,
            ]);
        }
    }

    public function syncPackageTextBlocks(TripPackage $package, array $payload): void
    {
        $package->includes()->delete();
        collect($this->normalizeRepeaterInput($payload['includes'] ?? []))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->each(fn ($line) => TripInclude::create(['trip_package_id' => $package->id, 'content' => $line]));

        $package->excludes()->delete();
        collect($this->normalizeRepeaterInput($payload['excludes'] ?? []))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->each(fn ($line) => TripExclude::create(['trip_package_id' => $package->id, 'content' => $line]));

        $package->highlights()->delete();
        collect($this->normalizeRepeaterInput($payload['highlights'] ?? []))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->each(fn ($line) => TripHighlight::create([
                'trip_package_id' => $package->id,
                'title' => Str::limit($line, 255),
                'description' => $line,
            ]));
    }

    public function canonicalHotelIdsFromDays(Trip $trip): array
    {
        return $trip->days()
            ->orderBy('day_number')
            ->whereNotNull('hotel_id')
            ->pluck('hotel_id')
            ->map(fn ($hotelId) => (int) $hotelId)
            ->unique()
            ->values()
            ->all();
    }

    protected function normalizeRepeaterInput(mixed $value): array
    {
        if (is_array($value)) {
            return array_values($value);
        }

        if (is_string($value)) {
            return explode(PHP_EOL, $value);
        }

        return [];
    }
}
