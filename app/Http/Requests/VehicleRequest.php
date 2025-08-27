<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VehicleRequest extends FormRequest
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
            'transport_id'   => 'required|exists:transports,id',
            'car_model'      => 'required|string|max:255',
            'plate_number'   => 'required|string|max:50|unique:transport_vehicles,plate_number',
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
