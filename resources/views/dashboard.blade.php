@php use App\Enums\UserRole; @endphp
<x-app-layout>
    <section>
        <div class="description">
            <h1 style="font-weight:bold; font-size:35px;margin-top:5px">Let's go travel</h1>
           {{-- @if (Auth::user()->role === UserRole::ADMIN->value)
            <p>Hello admin</p>
        @endif--}}
        </div>
        <p style="font-size:18px;text-align:center;width:80%;">
            Unleash the traveler <span>inside you</span> & enjoy your dream vacation. <br>
            Plan your trips effortlessly — from destinations to bookings, all in one place. <br>
            It's the easiest way to organize your perfect journey and design your dream trip with personalized recommendations.
        </p>
    </section>
</x-app-layout>
