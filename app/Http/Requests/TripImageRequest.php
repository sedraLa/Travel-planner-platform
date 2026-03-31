<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TripImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'trip_id' => 'required|exists:trips,id',
            'image_path' => 'required|string|max:2048',
            'is_cover' => 'nullable|boolean',
        ];
    }
}
