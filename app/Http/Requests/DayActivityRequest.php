<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DayActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'trip_day_id' => 'required|exists:trip_days,id',
            'activity_id' => 'required|exists:activities,id',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after_or_equal:start_time',
            'notes' => 'nullable|string',
        ];
    }
}
