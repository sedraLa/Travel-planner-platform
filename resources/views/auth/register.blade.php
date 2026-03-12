@php
    use App\Enums\UserRole;
    

     $role = request('role', UserRole::USER->value);
    $isSpecialRole = in_array($role, [UserRole::DRIVER->value, UserRole::GUIDE->value], true);

    $driverStep2Fields = ['license_image', 'personal_image', 'license_category', 'experience', 'address', 'age'];
    $guideStep2Fields = ['bio', 'languages', 'years_of_experience', 'certificate_image', 'personal_image', 'age', 'address', 'is_tour_leader', 'specializations'];

    $step2HasErrors = $role === UserRole::DRIVER->value
        ? collect($driverStep2Fields)->contains(fn ($field) => $errors->has($field))
        : ($role === UserRole::GUIDE->value
            ? collect($guideStep2Fields)->contains(fn ($field) => $errors->has($field))
            : false);
@endphp

<x-guest-layout>
    <div class="register-wrapper">
        <div class="register-card">

            <h2 class="title" style="text-align:center;">Create Account</h2>
            <p class="subtitle" style="text-align:center;">Join the journey</p>

           @if($isSpecialRole)
                <div style="display:flex; gap:10px; margin-bottom:20px;">
                    <span id="step1Indicator" style="font-weight:bold; color:white; opacity: {{ $step2HasErrors ? '0.4' : '1' }}">Step 1</span>
                    <span id="step2Indicator" style="color:white; opacity: {{ $step2HasErrors ? '1' : '0.4' }}">Step 2</span>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
                @csrf

                <!-- STEP 1 -->
                 <div id="step1" style="display: {{ $step2HasErrors ? 'none' : 'block' }};">

                    <div class="form-grid">
                        <!-- Name -->
                        <div>
                            <x-input-label for="name" :value="__('Name')" /> <span style="color:red;">*</span>
                            <div class="input-icon-wrapper">
                               <img src="{{ asset('images/icons/user-group-solid-full.svg') }}">
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

                    <!-- Only SpecialRole get "Continue" -->
                     @if($isSpecialRole)
                        <button type="button" class="main-btn" style="margin-top:20px;" onclick="goToStep2()">Continue</button>
                    @endif
                </div>

                <!-- STEP 2 for Driver -->
@if(request('role') === UserRole::DRIVER->value)
<div id="step2" @if($errors->has('license_image')) style="display:block;" @else style="display:none;" @endif>

    <!-- license image -->
    <div class="form-grid full">
        <div>
            <x-input-label for="license_image" :value="__('License Image')" /> <span class="text-red-500">*</span>
            <input id="license_image" type="file" name="license_image"  required/>
            <x-input-error :messages="$errors->get('license_image')" />
        </div>

    <!--personal image-->
    <div>
    <x-input-label for="personal_image" :value="__('personal_image')"/> <span class="text-red-500">*</span>
    <input id="personal_image" type="file" name="personal_image" required/>
    <x-input-error :messages="$errors->get('personal_image')"/>
    </div>

    <!-- License Category -->
        <div class="full">
            <x-input-label for="license_category" value="License Category" /> <span class="text-red-500">*</span>
            <select id="license_category" name="license_category" required>
                <option value="">-- Select Category --</option>
                <option value="A" @if(old('license_category')=='A') selected @endif>A</option>
                <option value="B" @if(old('license_category')=='B') selected @endif>B</option>
            </select>
            <x-input-error :messages="$errors->get('license_category')" />
        </div>

        <!-- Experience -->
        <div class="full">
            <x-input-label for="experience" value="Experience" />
            <textarea id="experience" name="experience" rows="3">{{ old('experience') }}</textarea>
            <x-input-error :messages="$errors->get('experience')" />
        </div>

        <!-- Address -->
        <div>
            <x-input-label for="address" value="Address" />
            <x-text-input id="address" type="text" name="address" :value="old('address')" />
            <x-input-error :messages="$errors->get('address')" />
        </div>

        <!-- Age -->
        <div>
            <x-input-label for="age" value="Age" /> <span class="text-red-500">*</span>
            <x-text-input id="age" type="number" name="age" :value="old('age')" required />
            <x-input-error :messages="$errors->get('age')" />
        </div>
    </div>

    <!-- Note -->
    <p class="note full">Note: Driver registration will be reviewd by management before acceptance </p>

    <!-- Back button -->
    <div style="display:flex; gap:10px; margin-top:15px;">
        <button type="button" class="main-btn" onclick="goToStep1()">Back</button>
        <button type="submit" class="main-btn">Register Driver</button>
    </div>

</div>
@endif

  @if($role === UserRole::GUIDE->value)

<div id="step2" style="display: {{ $step2HasErrors ? 'block' : 'none' }};">

    <div class="form-grid full">

        <!-- Personal Image -->
        <div>
            <x-input-label for="personal_image" :value="__('Personal Image')" /> <span class="text-red-500">*</span>
            <input id="personal_image" type="file" name="personal_image" required>
            <x-input-error :messages="$errors->get('personal_image')" />
        </div>

        <!-- Certificate -->
        <div>
            <x-input-label for="certificate_image" :value="__('Certificate Image')" />
            <input id="certificate_image" type="file" name="certificate_image">
            <x-input-error :messages="$errors->get('certificate_image')" />
        </div>

        <!-- Languages -->
        <div>
            <x-input-label for="languages" value="Languages" />
            <x-text-input id="languages" type="text" name="languages" :value="old('languages')" />
        </div>

        <!-- Experience -->
        <div>
            <x-input-label for="years_of_experience" value="Years of Experience" />
            <x-text-input id="years_of_experience" type="number" name="years_of_experience" />
        </div>

        <!-- Age -->
        <div>
            <x-input-label for="age" value="Age" /> <span class="text-red-500">*</span>
            <x-text-input id="age" type="number" name="age" required />
        </div>

        <!-- Address -->
        <div>
            <x-input-label for="address" value="Address" />
            <x-text-input id="address" type="text" name="address" />
        </div>

        <!-- Bio -->
        <div class="full">
            <x-input-label for="bio" value="Bio" />
            <textarea id="bio" name="bio"></textarea>
        </div>

        <!-- Tour leader -->
        <div class="full">
            <label class="tour-leader-toggle">
                <input type="checkbox" name="is_tour_leader" value="1" id="is_tour_leader">
                <span class="toggle-track">
                    <span class="toggle-thumb"></span>
                </span>
                <span class="toggle-label">I am a Tour Leader</span>
            </label>
        </div>

          <!-- Specializations Chips -->
        <div class="full">
            <x-input-label value="Specializations" />

            <div class="spec-chips-wrap">
                @foreach($specializations as $special)
                    @php $isChecked = in_array($special->id, old('specializations', [])); @endphp
                    <label class="spec-chip {{ $isChecked ? 'spec-chip--active' : '' }}">
                        <input
                            type="checkbox"
                            name="specializations[]"
                            value="{{ $special->id }}"
                            @checked($isChecked)
                            onchange="this.closest('label').classList.toggle('spec-chip--active', this.checked)"
                        >
                        {{ $special->name }}
                    </label>
                @endforeach
            </div>

            <x-input-error :messages="$errors->get('specializations')" />
        </div>



    </div>

    <p class="note full">Note: Guide registration will be reviewed by management before acceptance.</p>

    <div style="display:flex; gap:10px; margin-top:15px;">
        <button type="button" class="main-btn" onclick="goToStep1()">Back</button>
        <button type="submit" class="main-btn">Register Guide</button>
    </div>

</div>

@endif

<!-- Send role with the form -->
<input type="hidden" name="role" value="{{ request('role', UserRole::USER->value) }}">

<!-- Traveler submits normally -->
     @if(!$isSpecialRole)
                    <button class="main-btn">Register</button>
                @endif

                <a href="{{ route('login') }}" class="back-link" style="text-align:center;">Already registered?</a>
            </form>
  </div>
</div>




 <style>
        span {
            color:red;
        }
    </style>

    <script>
        function goToStep2() {
            const step1 = document.getElementById('step1');
            const inputs = step1.querySelectorAll('input, select, textarea');
            let valid = true;

            inputs.forEach(input => {
                if (!input.checkValidity()) {
                    input.reportValidity();
                    valid = false;
                }
            });

            if (!valid) return;

            step1.style.display = 'none';
            document.getElementById('step2').style.display = 'block';
            document.getElementById('step1Indicator').style.opacity = '0.4';
            document.getElementById('step2Indicator').style.opacity = '1';
        }
    //for back
   function goToStep1() {
            document.getElementById('step2').style.display = 'none';
            document.getElementById('step1').style.display = 'block';
            document.getElementById('step1Indicator').style.opacity = '1';
            document.getElementById('step2Indicator').style.opacity = '0.4';
        }
    document.querySelector('form').addEventListener('submit', function(e) {
            const specialRole = @json($isSpecialRole);
            const step2 = document.getElementById('step2');

            if (specialRole && step2 && step2.style.display === 'none') {
                e.preventDefault();
                alert('Please complete Step 2 before submitting.');
            }
        });
    </script>

<!-- countries javascript library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/country-region-selector/0.4.1/crs.min.js"></script>




<style>
.tour-leader-toggle {
    display: flex;
    align-items: center;
    gap: 12px;
    cursor: pointer;
    user-select: none;
    width: fit-content;
}

/* إخفاء الـ checkbox الأصلي */
.tour-leader-toggle input[type="checkbox"] {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
}

/* الـ track (الخلفية) */
.toggle-track {
    position: relative;
    width: 48px;
    height: 26px;
    background: rgba(255,255,255,0.15);
    border: 1px solid rgba(255,255,255,0.25);
    border-radius: 999px;
    transition: background 0.3s, border-color 0.3s;
    flex-shrink: 0;
}

/* الدايرة */
.toggle-thumb {
    position: absolute;
    top: 3px;
    left: 3px;
    width: 18px;
    height: 18px;
    background: white;
    border-radius: 50%;
    transition: transform 0.3s cubic-bezier(.4,0,.2,1), background 0.3s;
    box-shadow: 0 1px 4px rgba(0,0,0,0.25);
}

/* وقت يكون checked */
.tour-leader-toggle input:checked ~ .toggle-track {
    background: #4ade80;
    border-color: #4ade80;
}

.tour-leader-toggle input:checked ~ .toggle-track .toggle-thumb {
    transform: translateX(22px);
    background: white;
}

/* hover effect */
.tour-leader-toggle:hover .toggle-track {
    border-color: rgba(255,255,255,0.5);
}

.toggle-label {
    color: white;
    font-size: 0.95rem;
    font-weight: 500;
}
</style>


<style>
/* ===== Specialization Chips ===== */
.spec-chips-wrap {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.spec-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    border-radius: 999px;
    border: 2px solid rgba(255,255,255,0.25);
    background: rgba(255,255,255,0.08);
    color: rgba(255,255,255,0.75);
    font-size: 0.88rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    user-select: none;
}

.spec-chip:hover {
    border-color: rgba(255,255,255,0.5);
    background: rgba(255,255,255,0.15);
    color: white;
}

/* إخفاء الـ checkbox الأصلي */
.spec-chip input[type="checkbox"] {
    display: none;
}

/* وقت يكون selected */
.spec-chip--active {
    background: rgba(255,255,255,0.95) !important;
    border-color: white !important;
    color: #1E3A8A !important;
    font-weight: 700 !important;
    box-shadow: 0 2px 10px rgba(0,0,0,0.15);
}

.spec-chip--active::before {
    content: '✓';
    font-size: 0.8rem;
    font-weight: 800;
}
</style>

</x-guest-layout>