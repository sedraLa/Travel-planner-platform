@php
    use App\Enums\UserRole;
@endphp

<x-guest-layout>
    <div class="register-wrapper">
        <div class="register-card">

            <h2 class="title" style="text-align:center;">Create Account</h2>
            <p class="subtitle" style="text-align:center;">Join the journey</p>

            <!-- Indicators -->
            @if(request('role') === UserRole::DRIVER->value)
                <div style="display:flex; gap:10px; margin-bottom:20px;">
                    <span id="step1Indicator" style="font-weight:bold; color:white; @if($errors->any() && !$errors->has('license_image')) opacity:1; @else opacity:1; @endif">Step 1</span>
                    <span id="step2Indicator" style="opacity:0.4; color:white;">Step 2</span>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
                @csrf

                <!-- STEP 1 -->
                <div id="step1" @if($errors->any() && !$errors->has('license_image')) style="display:block" @endif>

                    <div class="form-grid">
                        <!-- Name -->
                        <div>
                            <x-input-label for="name" :value="__('Name')" /> <span style="color:red;">*</span>
                            <div class="input-icon-wrapper">
                                <img src="/icons/user.svg">
                                <x-text-input id="name" type="text" name="name" :value="old('name')" />
                            </div>
                            <x-input-error :messages="$errors->get('name')" />
                        </div>

                        <!-- Last Name -->
                        <div>
                            <x-input-label for="last_name" :value="__('Last Name')" /> <span class="text-red-500">*</span>
                            <div class="input-icon-wrapper">
                                <img src="/icons/user.svg">
                                <x-text-input id="last_name" type="text" name="last_name" :value="old('last_name')" />
                            </div>
                            <x-input-error :messages="$errors->get('last_name')" />
                        </div>

                        <!-- Phone -->
                        <div>
                            <x-input-label for="phone_number" :value="__('Phone Number')" />
                            <div class="input-icon-wrapper">
                                <img src="/icons/phone.svg">
                                <x-text-input id="phone_number" type="text" name="phone_number" :value="old('phone_number')" />
                            </div>
                            <x-input-error :messages="$errors->get('phone_number')" />
                        </div>

                        <!-- Country -->
                        <div class="mt-4">
                            <x-input-label for="country" :value="__('Country')" /> <span class="text-red-500">*</span>
                            <div class="input-icon-wrapper">
                                <select id="country" name="country" class="crs-country block mt-1 w-full rounded-md border border-gray-300 bg-white shadow-sm
                                    focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 h-10"
                                    data-region-id="region" data-default-option="Select your country">
                                </select>
                            </div>
                            <x-input-error :messages="$errors->get('country')" />
                        </div>

                        <!-- Email -->
                        <div class="full">
                            <x-input-label for="email" :value="__('Email')" /> <span class="text-red-500">*</span>
                            <div class="input-icon-wrapper">
                                <img src="/icons/email.svg">
                                <x-text-input id="email" type="email" name="email" :value="old('email')" />
                            </div>
                            <x-input-error :messages="$errors->get('email')" />
                        </div>

                        <!-- Password -->
                        <div>
                            <x-input-label for="password" :value="__('Password')" /> <span class="text-red-500">*</span>
                            <div class="input-icon-wrapper">
                                <img src="/icons/lock.svg">
                                <x-text-input id="password" type="password" name="password" />
                            </div>
                            <x-input-error :messages="$errors->get('password')" />
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <x-input-label for="password_confirmation" :value="__('Confirm Password')" /> <span class="text-red-500">*</span>
                            <div class="input-icon-wrapper">
                                <img src="/icons/lock.svg">
                                <x-text-input id="password_confirmation" type="password" name="password_confirmation" />
                            </div>
                            <x-input-error :messages="$errors->get('password_confirmation')" />
                        </div>
                    </div>

                    <!-- Only drivers get "Continue" -->
                    @if(request('role') === UserRole::DRIVER->value)
                    <button type="button" class="main-btn" style="margin-top:20px;" onclick="goToStep2()">
                        Continue
                    </button>
                    @endif
                </div>

                <!-- STEP 2 for Driver -->
                @if(request('role') === UserRole::DRIVER->value)
                <div id="step2" @if($errors->has('license_image')) style="display:block;" @else style="display:none;" @endif>

                    <div class="form-grid full">
                        <div class="full">
                            <x-input-label for="license_image" :value="__('License Image')" /> <span class="text-red-500">*</span>
                            <input id="license_image" type="file" name="license_image" />
                            <x-input-error :messages="$errors->get('license_image')" />
                        </div>

                        <div class="full">
                            <x-input-label for="license_category" value="License Category" /> <span class="text-red-500">*</span>
                            <select id="license_category" name="license_category">
                                <option value="">-- Select Category --</option>
                                <option value="A" @if(old('license_category')=='A') selected @endif>A</option>
                                <option value="B" @if(old('license_category')=='B') selected @endif>B</option>
                            </select>
                            <x-input-error :messages="$errors->get('license_category')" />
                        </div>

                        <div class="full">
                            <x-input-label for="experience" value="Experience" />
                            <textarea id="experience" name="experience" rows="3">{{ old('experience') }}</textarea>
                            <x-input-error :messages="$errors->get('experience')" />
                        </div>

                        <div>
                            <x-input-label for="address" value="Address" />
                            <x-text-input id="address" type="text" name="address" :value="old('address')" />
                            <x-input-error :messages="$errors->get('address')" />
                        </div>

                        <div>
                            <x-input-label for="age" value="Age" />
                            <x-text-input id="age" type="number" name="age" :value="old('age')" />
                            <x-input-error :messages="$errors->get('age')" />
                        </div>
                    </div>

                    <p class="note full">Note: Driver registration will be reviewd by management before acceptance </p>

                    <button type="submit" class="main-btn" style="margin-top:15px;">
                        Register Driver
                    </button>

                </div>
                @endif

                <input type="hidden" name="role" value="{{ request('role', UserRole::USER->value) }}">

                <!-- Traveler submits normally -->
                @if(request('role') !== UserRole::DRIVER->value)
                    <button class="main-btn">Register</button>
                @endif

                <a href="{{ route('login') }}" class="back-link" style="text-align:center;">Already registered?</a>
            </form>

            <script src="https://cdnjs.cloudflare.com/ajax/libs/country-region-selector/0.4.1/crs.min.js"></script>
            <script>
                function goToStep2() {
                    document.getElementById('step1').style.display = 'none';
                    document.getElementById('step2').style.display = 'block';

                    document.getElementById('step1Indicator').style.opacity = "0.4";
                    document.getElementById('step2Indicator').style.opacity = "1";
                }
            </script>


        </div>
    </div>
    <style>
        span {
            color:red;
        }
        </style>
</x-guest-layout>
