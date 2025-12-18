<x-guest-layout>
    <div class="register-wrapper">
        <div class="register-card">

            <h2 class="title" style="text-align:center;">Welcome Back</h2>
            <p class="subtitle" style="text-align:center;">Log in to continue your journey</p>

            {{-- Success Message --}}
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-800 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-grid">

                    <!-- Email -->
                    <div class="full">
                        <x-input-label for="email" :value="__('Email')" />
                        <div class="input-icon-wrapper">
                            <img src="/icons/email.svg">
                            <x-text-input
                                id="email"
                                type="email"
                                name="email"
                                :value="old('email')"
                                required
                                autofocus
                                autocomplete="username"
                            />
                        </div>

                        <x-input-error :messages="$errors->get('email')" />
                    </div>

                    <!-- Password -->
                    <div class="full">
                        <x-input-label for="password" :value="__('Password')" />
                        <div class="input-icon-wrapper">
                            <img src="/icons/lock.svg">
                            <x-text-input
                                id="password"
                                type="password"
                                name="password"
                                required
                                autocomplete="current-password"
                            />
                        </div>

                        <x-input-error :messages="$errors->get('password')" />
                    </div>

                </div>

                <button class="main-btn" style="margin-top:20px;">
                    Log in
                </button>

                <a href="{{ route('register.select-role') }}" class="back-link" style="text-align:center;">
                    Create a new account
                </a>

            </form>

        </div>
    </div>
</x-guest-layout>
