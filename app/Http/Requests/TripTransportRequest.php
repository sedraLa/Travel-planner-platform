<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TripTransportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'trip_id' => 'required|exists:trips,id',
            'transport_vehicle_id' => 'required|exists:transport_vehicles,id',
            'driver_id' => 'nullable|exists:drivers,id',
            'transport_type' => 'required|string|max:255',
            'departure_time' => 'nullable|date_format:H:i',
            'return_time' => 'nullable|date_format:H:i|after_or_equal:departure_time',
            'notes' => 'nullable|string',
        ];
    }
}
