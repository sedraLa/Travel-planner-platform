@php
    use App\Enums\UserRole;
@endphp
<x-guest-layout>
  <div class="login-container">
    <div class="left">
      <h1>Welcome</h1>
      <p>Choose how you want to register.</p>
    </div>

    <div class="form-login">
      <h2 style="color:white; text-align:center; margin-bottom:10px;">Register As</h2>

      <div style="display:flex; flex-direction:column; gap:12px; width:100%;">
        <a href="{{ route('register', ['role' => UserRole::USER->value]) }}" class="custom-primary-button" style="text-align:center;">
    I'm a Traveler
        </a>

        <a href="{{ route('register', ['role' => UserRole::DRIVER->value]) }}" class="custom-primary-button" style="background-color:#2ecc71; text-align:center;">
    I'm a Driver
        </a>
        <a href="{{ route('login') }}" style="color:white; text-align:center; margin-top:8px;">Already have an account? Login</a>
      </div>
    </div>
  </div>
</x-guest-layout>
