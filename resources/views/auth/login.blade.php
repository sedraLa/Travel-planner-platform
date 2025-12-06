<x-guest-layout>
             {{-- Success Message --}}
                   @if (session('success') && session('from') === 'set_primary')
                   <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-800 rounded">
                       {{ session('success') }}
                   </div>
               @elseif (session('success'))
                   <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-800 rounded">
                       {{ session('success') }}
                   </div>
               @endif
    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email">{{ __('Email') }}</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
                autocomplete="username"
            />
            <!-- Error Message for Email -->
            <x-input-error :messages="$errors->get('email')"/>
        </div>

        <!-- Password -->
        <div>
            <label for="password">{{ __('Password') }}</label>
            <input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="current-password"
            />
            <!-- Error Message for Password -->
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500 text-sm" />
        </div>

        <!-- Remember Me -->
        {{--<div>
            <label for="remember_me">
                <input id="remember_me" type="checkbox" name="remember">
                <span>{{ __('Remember me') }}</span>
            </label>
        </div>
        --}}

       {{-- <div>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif--}}

                <div>
                    <x-primary-button>
                        {{ __('Log in') }}
                    </x-primary-button>
                </div>

            <a href="{{ route('register.select-role') }}">
                {{ __("Create a new account") }}
            </a>
        </div>
    </form>
</x-guest-layout>
