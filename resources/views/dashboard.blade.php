@php use App\Enums\UserRole; @endphp
<x-app-layout>
    <section>
        <div class="description">
            <h1 style="font-weight:bold; font-size:35px;margin-top:5px">Let's go travel</h1>
            @if (Auth::user()->role === UserRole::ADMIN->value)
            <p>Hello admin</p>
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
    <style>
        section {
    width: 60%;
    min-width: 300px;
    margin: 10px auto;
    display: flex;
    justify-content: center; 
     align-items: center; 
    height: 73vh;
    gap: 10px;
    flex-direction: column;
}
        </style>
</x-app-layout>