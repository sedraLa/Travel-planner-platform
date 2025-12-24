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
                                <img src="{{asset('images/icons/user-group-solid-full.svg')}}">
                                <x-text-input id="name" type="text" name="name" :value="old('name')" required/>
                            </div>
                            <x-input-error :messages="$errors->get('name')" />
                        </div>

                        <!-- Last Name -->
                        <div>
                            <x-input-label for="last_name" :value="__('Last Name')" /> <span class="text-red-500">*</span>
                            <div class="input-icon-wrapper">
                                <img src="{{asset('images/icons/user-group-solid-full.svg')}}">
                                <x-text-input id="last_name" type="text" name="last_name" :value="old('last_name')" required />
                            </div>
                            <x-input-error :messages="$errors->get('last_name')" />
                        </div>

                        <!-- Phone -->
                        <div>
                            <x-input-label for="phone_number" :value="__('Phone Number')" /> <span class="text-red-500">*</span>
                            <div class="input-icon-wrapper">
                                <img src="{{asset('images/icons/phone-solid-full.svg')}}">
                                <x-text-input id="phone_number" type="text" name="phone_number" :value="old('phone_number')" required />
                            </div>
                            <x-input-error :messages="$errors->get('phone_number')" />
                        </div>

                        <!-- Country -->
                        <div class="mt-4">
                            <x-input-label for="country" :value="__('Country')" /> <span class="text-red-500">*</span>
                            <div class="input-icon-wrapper">
                                <select id="country" name="country" class="crs-country block mt-1 w-full rounded-md border border-gray-300 bg-white shadow-sm
                                    focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 h-10"
                                    data-region-id="region" data-default-option="Select your country" required>
                                </select>
                            </div>
                            <x-input-error :messages="$errors->get('country')" />
                        </div>

                        <!-- Email -->
                        <div class="full">
                            <x-input-label for="email" :value="__('Email')" /> <span class="text-red-500">*</span>
                            <div class="input-icon-wrapper">
                                <img src="{{asset('images/icons/email.png')}}">
                                <x-text-input id="email" type="email" name="email" :value="old('email')" required />
                            </div>
                            <x-input-error :messages="$errors->get('email')" />
                        </div>

                        <!-- Password -->
                        <div>
                            <x-input-label for="password" :value="__('Password')" /> <span class="text-red-500">*</span>
                            <div class="input-icon-wrapper">
                                <img src="{{asset('images/icons/policies.png')}}">
                                <x-text-input id="password" type="password" name="password" required />
                            </div>
                            <x-input-error :messages="$errors->get('password')" />
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <x-input-label for="password_confirmation" :value="__('Confirm Password')" /> <span class="text-red-500">*</span>
                            <div class="input-icon-wrapper">
                                <img src="{{asset('images/icons/policies.png')}}">
                                <x-text-input id="password_confirmation" type="password" name="password_confirmation" required />
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
            <input id="license_image" type="file" name="license_image"  required/>
            <x-input-error :messages="$errors->get('license_image')" />
        </div>

        <div class="full">
            <x-input-label for="license_category" value="License Category" /> <span class="text-red-500">*</span>
            <select id="license_category" name="license_category" required>
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
            <x-input-label for="age" value="Age" /> <span class="text-red-500">*</span>
            <x-text-input id="age" type="number" name="age" :value="old('age')" required />
            <x-input-error :messages="$errors->get('age')" />
        </div>
    </div>

    <p class="note full">Note: Driver registration will be reviewd by management before acceptance </p>

    <div style="display:flex; gap:10px; margin-top:15px;">
        <button type="button" class="main-btn" onclick="goToStep1()">Back</button>
        <button type="submit" class="main-btn">Register Driver</button>
    </div>

</div>
@endif
<script>
    function goToStep2() {
    const step1 = document.getElementById('step1');
    const inputs = step1.querySelectorAll('input, select, textarea');
    let valid = true;

    // تحقق من صحة كل الحقول
    inputs.forEach(input => {
        if (!input.checkValidity()) {
            input.reportValidity(); // يعرض رسالة required أو أي validation
            valid = false;
            return false; // يوقف التحقق عند أول خطأ
        }
    });

    if (!valid) return; // ما نروح Step 2 إذا فيه خطأ

    // إذا كل الحقول صحيحة
    step1.style.display = 'none';
    document.getElementById('step2').style.display = 'block';
    document.getElementById('step1Indicator').style.opacity = "0.4";
    document.getElementById('step2Indicator').style.opacity = "1";
}

    function goToStep1() {
        document.getElementById('step2').style.display = 'none';
        document.getElementById('step1').style.display = 'block';
        document.getElementById('step1Indicator').style.opacity = "1";
        document.getElementById('step2Indicator').style.opacity = "0.4";
    }

    // تأكد إن زر Register Driver يرسل الفورم فقط إذا Step 2 ظاهر
    document.querySelector('form').addEventListener('submit', function(e){
        if(document.getElementById('step2').style.display === 'none' && '{{ request("role") }}' === '{{ UserRole::DRIVER->value }}'){
            e.preventDefault(); // يمنع الفورم إذا Step 2 مخفية
            alert("Please complete Step 2 before submitting.");
        }
    });
</script>



                <input type="hidden" name="role" value="{{ request('role', UserRole::USER->value) }}">

                <!-- Traveler submits normally -->
                @if(request('role') !== UserRole::DRIVER->value)
                    <button class="main-btn">Register</button>
                @endif

                <a href="{{ route('login') }}" class="back-link" style="text-align:center;">Already registered?</a>
            </form>

            <script src="https://cdnjs.cloudflare.com/ajax/libs/country-region-selector/0.4.1/crs.min.js"></script>


        </div>
    </div>
    <style>
        span {
            color:red;
        }
        </style>
</x-guest-layout>
