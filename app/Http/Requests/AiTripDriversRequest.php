<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AiTripDriversRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vehicle_type' => ['nullable', 'string', 'max:100', 'required_without:vehicle_capacity'],
            'vehicle_capacity' => ['nullable', 'integer', 'min:1', 'required_without:vehicle_type'],
            'trip_type' => ['required', 'string', 'in:single_day,multi_day'],
            'road_type' => ['required', 'string', 'in:city,mountain,off_road'],
        ];
    }
}
