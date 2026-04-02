<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TripRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tripId = $this->route('trip')?->id ?? $this->route('trip');

        return [
            'destination_id' => 'required|exists:destinations,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('trips', 'slug')->ignore($tripId),
            ],
            'duration_days' => 'required|integer|min:1',
            'category' => 'nullable|string|max:255',
            'max_participants' => 'nullable|integer|min:1',
            'meeting_point_description' => 'nullable|string',
            'meeting_point_address' => 'nullable|string|max:255',
            'is_ai_generated' => 'nullable|boolean',
            'ai_prompt' => 'nullable|string',
            'status' => 'nullable|string|max:100',
        ];
    }
}
