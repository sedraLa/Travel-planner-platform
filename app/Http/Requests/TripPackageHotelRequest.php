<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TripPackageHotelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'trip_package_id' => 'required|exists:trip_packages,id',
            'hotel_id' => 'required|exists:hotels,id',
            'room_type' => 'nullable|string|max:255',
            'amenities' => 'nullable|array',
            'meal_plan' => 'nullable|string|max:255',
        ];
    }
}
