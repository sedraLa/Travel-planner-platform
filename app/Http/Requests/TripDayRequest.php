<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TripDayRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'trip_id' => 'required|exists:trips,id',
            'day_number' => 'required|integer|min:1',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'highlights' => 'nullable|array',
            'highlights.*' => 'string|max:255',
            'hotel_id' => 'nullable|exists:hotels,id',
        ];
    }
}
