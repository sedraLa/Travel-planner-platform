<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="('Name')" />
            <x-text-input id="name" type="text" name="name" :value="old('name')" required autofocus
                autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" />
        </div>
        <!-- Last Name -->
        <div class="mt-4">
            <x-input-label for="last_name" :value="('Last Name')" />
            <x-text-input id="last_name" type="text" name="last_name" :value="old('last_name')" required
                autocomplete="family-name" />
            <x-input-error :messages="$errors->get('last_name')" />
        </div>


        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="('Email')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="('Password')" />

            <x-text-input id="password" type="password" name="password" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="('Confirm Password')" />

            <x-text-input id="password_confirmation" type="password" name="password_confirmation" required
                autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" />
        </div>

        <!-- Country -->
        <div class="mt-4">
            <x-input-label for="country" :value="__('Country')" />
            <select id="country" name="country" class="crs-country block mt-1 w-full rounded-md border border-gray-300 bg-white shadow-sm
        focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 h-10" data-region-id="region"
                data-default-option="Select your country">
            </select>
            <x-input-error :messages="$errors->get('country')" />
        </div>


        <!-- Phone Number -->
        <div class="mt-4">
            <x-input-label for="phone_number" :value="('Phone Number')" />
            <x-text-input id="phone_number" type="text" name="phone_number" :value="old('phone_number')" required
                autocomplete="tel" />
            <x-input-error :messages="$errors->get('phone_number')" />
        </div>

        <div>
            <x-primary-button>
                {{ ('Register') }}
            </x-primary-button>
            <a href="{{ route('login') }}">
                {{ ('Already registered?') }}
            </a>
        </div>
    </form>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/country-region-selector/0.4.1/crs.min.js"></script>
</x-guest-layout>