<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignmentRequest extends FormRequest
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
              'transport_vehicle_id' => 'required|exists:transport_vehicles,id',
              'shift_template_id' => 'required|exists:shift_templates,id',
              'driver_id' => 'required|exists:drivers,id|unique:assignments,driver_id',
        ];
    }
}
