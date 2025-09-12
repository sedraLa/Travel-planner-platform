<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $vehicleId = $this->route('id'); // اسم الباراميتر بالـ route

        return [
            'transport_id'   => 'required|exists:transports,id',
            'car_model'      => 'required|string|max:255',
            'plate_number'   => [
                'required',
                'string',
                'max:50',
                Rule::unique('transport_vehicles', 'plate_number')->ignore($vehicleId),
            ],
            'driver_name'    => 'required|string|max:255',
            'driver_contact' => 'required|string|max:20',
            'max_passengers' => 'required|integer|min:1',
            'base_price'     => 'required|numeric|min:0',
            'price_per_km'   => 'required|numeric|min:0',
            'category'       => 'nullable|string|max:100',
            'image'          => 'nullable|image|mimes:jpeg,png,jpg,gif',
        ];
    }
}
