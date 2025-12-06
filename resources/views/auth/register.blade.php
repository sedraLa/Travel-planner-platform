<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
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

        <div>
            <x-primary-button>
                {{ __('Register') }}
            </x-primary-button>
            <a href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>
        </div>
    </form>
</x-guest-layout>
