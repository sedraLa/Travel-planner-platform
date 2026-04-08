<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AiTripDaysActivitiesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'days' => ['required', 'array', 'min:1'],
            'days.*.day_number' => ['required', 'integer', 'min:1'],
            'days.*.title' => ['nullable', 'string', 'max:255'],
            'days.*.description' => ['nullable', 'string'],
            'days.*.hotel_id' => ['nullable', 'exists:hotels,id'],
            'days.*.activities' => ['nullable', 'array'],
            'days.*.activities.*.id' => ['nullable', 'integer', 'exists:day_activities,id'],
            'days.*.activities.*.activity_id' => ['nullable', 'exists:activities,id'],
            'days.*.activities.*.start_time' => ['nullable', 'date_format:H:i'],
            'days.*.activities.*.end_time' => ['nullable', 'date_format:H:i'],
            'days.*.activities.*.notes' => ['nullable', 'string'],
        ];
    }
}
