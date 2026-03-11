<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GuideRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return ture;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status'           => 'nullable|string|in:pending,approved,rejected',
            'earnings_balance' => 'nullable|numeric|min:0',
            'date_of_hire'     => 'nullable|date',
            'last_trip_at' => 'nullable|date',
            'total_trips_count' => 'nullable|integer|min:0',
        ];
    }
}
