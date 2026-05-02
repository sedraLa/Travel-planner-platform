<?php

namespace App\Services\AiTrip;

use App\Models\Trip;
use App\Models\TripSchedule;
use Illuminate\Support\Facades\DB;

class TripScheduleService
{
    public function saveSchedules(Trip $trip, array $payload): void
    {
        DB::transaction(function () use ($trip, $payload) {
            $keepIds = [];

            foreach (($payload['schedules'] ?? []) as $schedulePayload) {
                $schedule = TripSchedule::query()->updateOrCreate(
                    ['id' => $schedulePayload['id'] ?? null, 'trip_id' => $trip->id],
                    [
                        'start_date' => $schedulePayload['start_date'],
                        'end_date' => $schedulePayload['end_date'],
                        'booking_deadline' => $schedulePayload['booking_deadline'] ?? null,
                        'available_seats' => $schedulePayload['available_seats'] ?? null,
                        'price_modifier' => $schedulePayload['price_modifier'] ?? 0,
                        'status' => $schedulePayload['status'] ?? 'available',
                    ]
                );

                $keepIds[] = $schedule->id;
            }

            $trip->schedules()->whereNotIn('id', $keepIds ?: [0])->delete();
        });
    }
}
