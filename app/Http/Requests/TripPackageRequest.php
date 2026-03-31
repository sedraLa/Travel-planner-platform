<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TripPackageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'trip_id' => 'required|exists:trips,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ];
    }
}
