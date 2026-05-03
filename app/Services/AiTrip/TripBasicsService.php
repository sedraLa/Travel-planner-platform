<?php

namespace App\Services\AiTrip;

use App\Models\Trip;
use Illuminate\Support\Facades\DB;

class TripBasicsService
{
    public function saveBasics(Trip $trip, array $payload): void
    {
        $destinationIds = array_values(array_unique(array_map('intval', $payload['destination_ids'])));
        $primaryDestinationId = (int) $payload['destination_id'];

        //check primary destination is in array
        if (! in_array($primaryDestinationId, $destinationIds, true)) {
            array_unshift($destinationIds, $primaryDestinationId);
        }

        $destinationIds = array_values(array_unique([$primaryDestinationId, ...$destinationIds]));

        DB::transaction(function () use ($trip, $payload, $destinationIds, $primaryDestinationId) {
            $trip->update(
                collect($payload)
                    ->except('destination_ids')
                    ->put('destination_id', $primaryDestinationId)
                    ->put('status', 'draft')
                    ->all()
            );

           
            $this->syncDestinations($trip, $destinationIds);
        });
    }

    public function syncDestinations(Trip $trip, array $destinationIds): void
    {
        //pivot data
        $trip->itineraryDestinations()->sync(
            collect($destinationIds)->values()->mapWithKeys(fn (int $destinationId, int $index) => [
                $destinationId => ['sort_order' => $index + 1],
            ])->all()
        );
    }
}
