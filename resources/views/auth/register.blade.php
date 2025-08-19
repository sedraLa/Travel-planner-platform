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
            <x-input-label for="country" :value="('Country')" />
            <select id="country" name="country"
                class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                required>
                <option value="">-- Select your country --</option>
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

        <div class="mt-4">
            <x-primary-button>
                {{ ('Register') }}
            </x-primary-button>
            <a href="{{ route('login') }}">
                {{ ('Already registered?') }}
            </a>
        </div>
    </form>

    <!-- سكربت الدول -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const countries = [
                "Lebanon", "France", "United States", "Canada",
                "Germany", "Italy", "United Kingdom", "Bahrain"
            ];

            let countrySelect = document.getElementById("country");
            countries.forEach(c => {
                let opt = document.createElement("option");
                opt.value = c;
                opt.textContent = c;
                countrySelect.appendChild(opt);
            });

            // رجوع old value إذا موجود
            let oldVal = "{{ old('country') }}";
            if (oldVal) {
                countrySelect.value = oldVal;
            }
        });
    </script>
</x-guest-layout>