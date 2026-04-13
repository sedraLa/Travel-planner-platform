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
            'user_id' => 'required|exists:users,id',
            'day_activity_id' => 'required|exists:day_activities,id',
            'guide_id' => 'required|exists:guides,id',
            'status' => 'nullable|string|in:assigned,completed,cancelled',
            'guide_earning'=> 'nullable|numeric|min:0',
        ];
    }
}
