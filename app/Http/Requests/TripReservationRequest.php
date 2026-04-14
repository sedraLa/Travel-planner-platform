<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TripReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
             'user_id' => ['required', 'exists:users,id'],
            'trip_id' => ['required', 'exists:trips,id'],
            'trip_package_id' => ['required', 'exists:trip_packages,id'],
            'trip_schedule_id' => ['required', 'exists:trip_schedules,id'],

            'people_count' => ['required', 'integer', 'min:1'],

            'total_price' => ['required', 'numeric', 'min:0'],

            'status' => ['nullable', 'string', 'in:pending,cancelled,completed'],
      
        ];
    }
}
