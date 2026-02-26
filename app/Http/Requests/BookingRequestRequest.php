<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookingRequestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
             'reservation_id' => ['required', 'exists:reservations,id'],
             'driver_id'      => ['required', 'exists:drivers,id'],
             'status'         => ['required', 'string', 'in:sent,accepted,rejected,timed_out'],
             'expires_at'     => ['required', 'date', 'after:now'],
        ];
    }
}
