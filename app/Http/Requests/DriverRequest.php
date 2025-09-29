<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DriverRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'name'             => 'required|string|max:255',
            'age'              => 'nullable|integer|min:18|max:100',
            'address'          => 'nullable|string|max:255',
            'license_image'    => ($this->isMethod('post') ? 'required|' : 'nullable|') . 'image|mimes:jpg,jpeg,png|max:2048',
            'license_category' => 'nullable|string|max:255',
            'status'           => 'nullable|string|in:active,inactive',
            'date_of_hire'     => 'nullable|date',
            'experience'       => 'nullable|string',
            'phone'            => 'required|string|max:20',
            'email'            => [
                'required',
                'email',
                Rule::unique('drivers', 'email')->ignore($this->route('id')),
            ],
        ];
    }

    /**
     * Custom messages for validation errors (optional).
     */
    public function messages(): array
    {
        return [
            'name.required'          => 'Driver name is required',
            'license_image.required' => 'License image is required',
            'email.required'         => 'Email is required',
            'email.email'            => 'Email form is not correct',
            'email.unique'           => 'This email already exist',
            'phone.required'         => 'Phone number is required',
        ];
    }
}
