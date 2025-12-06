@php
    use App\Enums\UserRole;
@endphp
<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')"/>
        </div>


        <!-- lastName -->
        <div>
            <x-input-label for="las_tname" :value="__('last_name')" />
            <x-text-input id="last_name" type="text" name="last_name" :value="old('last_name')" required autofocus autocomplete="last_name" />
            <x-input-error :messages="$errors->get('last_name')"/>
        </div>

        <!-- phone number-->
        <div>
            <x-input-label for="phone_number" :value="__('phone_number')" />
            <x-text-input id="phone_number" type="text" name="phone_number" :value="old('phone_number')"  />
            <x-input-error :messages="$errors->get('phone_number')"/>
        </div>

        <!-- country-->
        <div>
            <x-input-label for="country" :value="__('country')" />
            <x-text-input id="country" type="text" name="country" :value="old('country')"  />
            <x-input-error :messages="$errors->get('country')"/>
        </div>


        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" />
        </div>

<!--driver fields-->
@if(request('role') === UserRole::DRIVER->value)
<div>
    <x-input-label for="license_image" :value="__('License Image')" />
    <input id="license_image" type="file" name="license_image" />
    <x-input-error :messages="$errors->get('license_image')" />
  </div>

  <div>
    <x-input-label for="license_category" value="License Category" />
                    <select id="license_category" name="license_category"
                        class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">-- Select Category --</option>
                        <option value="A" @selected(old('license_category') == 'A')>Category A </option>
                        <option value="B" @selected(old('license_category') == 'B')>Category B </option>
                    </select>

                    <x-input-label for="experience" value="Experience" />
                    <textarea id="experience" name="experience"
                        class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        rows="3" placeholder="Describe driver's experience">{{ old('experience') }}</textarea>
                        <x-input-label for="address" value="Address" />
                    <x-text-input id="address" type="text" name="address" :value="old('address')"
                        placeholder="Enter driver's address" />

                    <x-input-label for="age" value="Age" />
                    <x-text-input id="age" type="number" name="age" :value="old('age')" placeholder="e.g. 25" />
  </div>
  @endif


        <div>
            <x-primary-button>
                {{ __('Register') }}
            </x-primary-button>
            <a href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>
            <input type="hidden" name="role" value="{{ request('role', UserRole::USER->value) }}">
            @if(request('role') === UserRole::DRIVER->value)
  <p style="color: #ffd966; font-weight:600;">
    ملاحظة: تسجيل السائق سيُرسل لطلب مراجعة من الإدارة. لن تتمكن من الدخول حتى يتم الموافقة.
  </p>
@endif
        </div>
        
    </form>
</x-guest-layout>
