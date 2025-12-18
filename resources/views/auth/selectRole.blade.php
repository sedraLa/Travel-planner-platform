@php
    use App\Enums\UserRole;
@endphp
<x-guest-layout>
    <div class="register-wrapper">

        <div class="register-card">

            <!--<div class="icon-place">
                <img src="/images/globe-icon.png" alt="icon" class="card-icon">
            </div>-->

            <h2 class="title" style="text-align:center;">Join Our Platform</h2>
            <p class="subtitle" style="text-align:center;">Choose how you want to get started</p>

            <div class="options">

                <a href="{{ route('register', ['role' => UserRole::USER->value]) }}" class="option-box traveler">
                    <div class="icon">
                        <img src="{{asset('/images/icons/user-group-solid-full.svg')}}" alt="">
                    </div>
                    <div class="text">
                        <h3 style="text-align:left;">Traveler</h3>
                        <p>Book rides and explore new destinations</p>
                    </div>
                    <span class="arrow">›</span>
                </a>

                <a href="{{ route('register', ['role' => UserRole::DRIVER->value]) }}" class="option-box driver">
                    <div class="icon">
                        <img src="{{asset('/images/icons/user-group-solid-full.svg')}}" alt="">
                    </div>
                    <div class="text">
                        <h3 style="text-align:left;">Driver</h3>
                        <p>Join our driver network and earn</p>
                    </div>
                    <span class="arrow">›</span>
                </a>

            </div>

            <a href="{{ route('login') }}" class="back-link" style="text-align:center;">Back to Sign In</a>

            <p class="footer" style="text-align:center;">© 2024 TravelPlatform. All rights reserved.</p>

        </div>

    </div>
</x-guest-layout>
