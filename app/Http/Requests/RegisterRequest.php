<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;
use App\Enums\UserRole;

class RegisterRequest extends FormRequest
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
        $role = $this->input('role', UserRole::USER->value);

        $rules = [

            // User fields
            'name' => ['required', 'string', 'max:255'],

            'last_name' => ['required', 'string', 'max:255'],

            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],

            'password' => ['required', 'confirmed', Rules\Password::defaults()],

            'phone_number' => ['required', 'string', 'max:50'],

            'country' => ['required', 'string', 'max:100'],

            'role' => ['required', 'string', 'in:user,driver'],

        ];

        // Driver fields
        if ($role === UserRole::DRIVER->value) {

            $rules = array_merge($rules, [

                'license_category' => ['required', 'string', 'max:50'],

                'license_image' => [
                    'required',
                    'image',
                    'mimes:jpg,jpeg,png',
                    'max:2048'
                ],

                'personal_image' => [
                    'required',
                    'image',
                    'mimes:jpg,jpeg,png',
                    'max:2048'
                ],

                'experience' => ['nullable', 'string'],

                'address' => ['nullable', 'string', 'max:255'],

                'age' => ['nullable', 'integer', 'min:18', 'max:100'],

            ]);
        }

        return $rules;
    }
}
