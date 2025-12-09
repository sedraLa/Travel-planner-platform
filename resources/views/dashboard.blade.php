@php use App\Enums\UserRole; @endphp
<x-app-layout>
    <section>
        <div class="description">
            <h1 style="font-weight:bold; font-size:35px;margin-top:5px">Let's go travel</h1>
            @if (Auth::user()->role === UserRole::ADMIN->value)
            <p>Hello admin</p>

            @elseif (Auth::user()->role === UserRole::DRIVER->value)
                @php
                   $driver = Auth::user()->driver;
                @endphp

            @if($driver)
                  <p>Hello driver {{ $driver->user->name }}</p>
                  <h3>Your Current Car is {{ $driver->vehicle->car_model ?? 'N/A' }}</h3>
            @else
                         <p>Hello driver</p>
                         <h3>Your Current Car is N/A</h3>
            @endif




        @endif
        </div>

        @if (Auth::user()->role !== UserRole::DRIVER->value)
        <p style="font-size:18px;text-align:center;width:80%;">
            Unleash the traveler <span>inside you</span> & enjoy your dream vacation. <br>
            Plan your trips effortlessly — from destinations to bookings, all in one place. <br>
            It's the easiest way to organize your perfect journey and design your dream trip with personalized recommendations.
        </p>
        @endif
    </section>
</x-app-layout>