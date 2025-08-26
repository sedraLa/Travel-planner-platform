<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name'           => 'required|string|max:255|unique:transports,name,' . $this->id,
            'description'    => 'required|string',
            'type'           => 'required|string|max:100',
        ];

        if ($this->isMethod('post')) {
            // create
            $rules['image'] = 'required|image|mimes:jpeg,png,jpg,gif';
        } else {
            // update
            $rules['image'] = 'nullable|image|mimes:jpeg,png,jpg,gif';
        }

        return $rules;
    }
}
