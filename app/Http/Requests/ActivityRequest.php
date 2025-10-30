<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'destination_id' => 'required|exists:destinations,id',
            'description' => 'nullable|string',
            'duration' => 'nullable|numeric|min:0',
            'duration_unit' => 'nullable|string|max:50',
            'price' => 'nullable|numeric|min:0',
            'category' => 'nullable|string|max:100',
            'is_active' => 'required|boolean',

            // الوقت والتاريخ
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after_or_equal:start_time',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',

            'availability' => 'required|string|max:50',

            'guide_name' => 'nullable|string|max:255',
            'guide_language' => 'nullable|string|max:100',
            'contact_number' => 'nullable|string|max:50',

            'requirements' => 'nullable|string',

            'difficulty_level' => 'nullable|in:easy,moderate,hard',

            // amenities JSON
            'amenities' => 'nullable|array',
            'amenities.*' => 'string|max:100',

            'address' => 'required|string|max:255',
            'requires_booking' => 'required|boolean',

            'family_friendly' => 'required|string|in:all_ages,adults_only,families',

            'pets_allowed' => 'required|boolean',
            'highlights' => 'nullable|string',
        ];
    }
}
