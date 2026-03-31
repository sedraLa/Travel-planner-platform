<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TripScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'trip_id' => 'required|exists:trips,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'booking_deadline' => 'nullable|date|before_or_equal:start_date',
            'available_seats' => 'nullable|integer|min:0',
            'price_modifier' => 'nullable|numeric|between:-999.99,999.99',
            'status' => 'nullable|string|in:available,full,cancelled',
        ];
    }
}
