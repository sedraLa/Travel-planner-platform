<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TripExcludeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'trip_package_id' => 'required|exists:trip_packages,id',
            'content' => 'required|string|max:255',
        ];
    }
}
