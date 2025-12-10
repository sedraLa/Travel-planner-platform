@php
    use App\Enums\UserRole;
@endphp
<x-guest-layout>
    <div class="register-wrapper">
    
        <div class="register-card">
    
            <h2 class="title">Create Account</h2>
            <p class="subtitle">Join the journey</p>
    
            <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
                @csrf
    
                <div class="form-grid">
    
                    <!-- Name -->
                    <div>
                        <x-input-label for="name" :value="__('Name')" />
                        <div class="input-icon-wrapper">
                            <img src="/icons/user.svg">
                            <x-text-input id="name" type="text" name="name" :value="old('name')" required />
                        </div>
                        <x-input-error :messages="$errors->get('name')"/>
                    </div>
    
                    <!-- Last Name -->
                    <div>
                        <x-input-label for="last_name" :value="__('Last Name')" />
                        <div class="input-icon-wrapper">
                            <img src="/icons/user.svg">
                            <x-text-input id="last_name" type="text" name="last_name" :value="old('last_name')" required />
                        </div>
                        <x-input-error :messages="$errors->get('last_name')"/>
                    </div>
    
                    <!-- Phone -->
                    <div>
                        <x-input-label for="phone_number" :value="__('Phone Number')" />
                        <div class="input-icon-wrapper">
                            <img src="/icons/phone.svg">
                            <x-text-input id="phone_number" type="text" name="phone_number" :value="old('phone_number')" />
                        </div>
                        <x-input-error :messages="$errors->get('phone_number')"/>
                    </div>
        <!-- Country -->
        <div>
            <x-input-label for="country" :value="__('Country')" />
            <div class="input-icon-wrapper">
                <img src="/icons/location.svg">
                <x-text-input id="country" type="text" name="country" :value="old('country')" />
            </div>
            <x-input-error :messages="$errors->get('country')"/>
        </div>

        <!-- Email -->
        <div class="full">
            <x-input-label for="email" :value="__('Email')" />
            <div class="input-icon-wrapper">
                <img src="/icons/email.svg">
                <x-text-input id="email" type="email" name="email" :value="old('email')" required />
            </div>
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />
            <div class="input-icon-wrapper">
                <img src="/icons/lock.svg">
                <x-text-input id="password" type="password" name="password" required />
            </div>
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <div class="input-icon-wrapper">
                <img src="/icons/lock.svg">
                <x-text-input id="password_confirmation" type="password" name="password_confirmation" required />
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" />
        </div>

    </div>
    
                <!-- Driver Extra Fields -->
                @if(request('role') === App\Enums\UserRole::DRIVER->value)
                    <div class="form-grid full">
    
                        <div class="full">
                            <x-input-label for="license_image" :value="__('License Image')" />
                            <input id="license_image" type="file" name="license_image" />
                            <x-input-error :messages="$errors->get('license_image')" />
                        </div>
    
                        <div class="full">
                            <x-input-label for="license_category" value="License Category" />
                            <select id="license_category" name="license_category">
                                <option value="">-- Select Category --</option>
                                <option value="A">Category A</option>
                                <option value="B">Category B</option>
                            </select>
                        </div>
    
                        <div class="full">
                            <x-input-label for="experience" value="Experience" />
                            <textarea id="experience" name="experience" rows="3"></textarea>
                        </div>
    
                        <div>
                            <x-input-label for="address" value="Address" />
                            <x-text-input id="address" type="text" name="address" />
                        </div>
    
                        <div>
                            <x-input-label for="age" value="Age" />
                            <x-text-input id="age" type="number" name="age" />
                        </div>
    
                    </div>
    
                    <p class="note full">ملاحظة: تسجيل السائق سيتم مراجعته من الإدارة قبل القبول.</p>
                @endif
    
                <input type="hidden" name="role" value="{{ request('role', App\Enums\UserRole::USER->value) }}">
    
                <button class="main-btn">Register</button>
    
                <a href="{{ route('login') }}" class="back-link">Already registered?</a>
    
            </form>
    
        </div>
    
    </div>
    
        <div>
        
</x-guest-layout>
