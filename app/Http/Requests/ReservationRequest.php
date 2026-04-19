<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReservationRequest extends FormRequest
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
            'hotel_id' => 'required|exists:hotels,id',
            'room_type_id' => 'nullable|exists:room_types,id',
            'guest_count' => 'nullable|integer|min:1',
            'guests' => 'nullable|integer|min:1',
            'rooms_count' => 'nullable|integer|min:1',
            'check_in_date' => 'nullable|date|after_or_equal:today',
            'check_out_date' => 'nullable|date|after:check_in_date',
            'check_in' => 'nullable|date|after_or_equal:today',
            'check_out' => 'nullable|date|after:check_in',
            'name' => 'nullable|string',
            'last_name' => 'nullable|string',
            'email' => 'nullable|email',
            'phone_number' => 'nullable|string',
            'country' => 'nullable|string',
        ];
    }
}
