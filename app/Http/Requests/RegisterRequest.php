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

            'role' => ['required', 'string', 'in:user,driver,guide'],

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


        
        if ($role === UserRole::GUIDE->value) {

            $rules = array_merge($rules, [

                'bio' => ['required', 'string'],

                'languages' => ['required', 'string', 'max:255'],


                'years_of_experience' => ['required', 'integer', 'min:0'],

                'certificate_image' => [
                    'nullable',
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

                 'age' => ['required', 'integer', 'min:18', 'max:100'],

                'address' => ['required', 'string', 'max:255'],

                'is_tour_leader' => ['boolean'],

                 'specializations' => ['nullable', 'array'],
                 'specializations.*' => ['exists:specializations,id'],

            ]);
        }

        

        return $rules;
    }
     
     public function withValidator($validator)
      {
             $validator->after(function ($validator) {

             if ($this->input('role') !== UserRole::GUIDE->value) {
            return;
        }

                $isLeader = $this->input('is_tour_leader');
                $hasSpecializations = $this->input('specializations', []);
                
              if (!$isLeader && (empty($hasSpecializations) || count($hasSpecializations) === 0)) {
                  $validator->errors()->add(
                     'specializations',
                     'You must select either Tour Leader or at least one activity.'
                      );
                  }

              
    
          });
       }
}



