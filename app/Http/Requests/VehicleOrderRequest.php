<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VehicleOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'pickup_location'=>'required|string',
            'dropoff_location'=>'required|string',
            'pickup_datetime'=>'required|date|after:now',
            'passengers'=>'required|integer|min:1',
            'driver_status'    => 'nullable|string|in:pending,completed',
        ];
    }
}
