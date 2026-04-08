<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AiTripSchedulesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'schedules' => ['nullable', 'array'],
            'schedules.*.id' => ['nullable', 'exists:trip_schedules,id'],
            'schedules.*.start_date' => ['required', 'date'],
            'schedules.*.end_date' => ['required', 'date'],
            'schedules.*.booking_deadline' => ['nullable', 'date'],
            'schedules.*.available_seats' => ['nullable', 'integer', 'min:0'],
            'schedules.*.price_modifier' => ['nullable', 'numeric'],
            'schedules.*.status' => ['nullable', 'string', 'in:available,full,cancelled'],
        ];
    }
}
