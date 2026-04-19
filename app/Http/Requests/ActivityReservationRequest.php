<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ActivityReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [


            'activity_id' => ['required', 'exists:activities,id'],

            'activity_date' => ['required', 'date', 'after_or_equal:today'],

            'participants_count' => ['required', 'integer', 'min:1'],


            'status' => ['nullable', 'in:pending,confirmed,cancelled'],


             
        ];
    }
}