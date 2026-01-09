@php use App\Enums\UserRole; @endphp
<!-- Primary Navigation Menu -->
<div class="navigation">
    <nav x-data="{ open: false }">
        <!-- Logo -->
        <div>
            <a href="{{ route('dashboard') }}">
                <div class="logo-section">
                    <img class="logo" src="{{ asset('images/Triply.jpg') }}">
                    <h2 class="triply text-xl font-bold text-gray-800">Triply</h2>
                </div>
            </a>
        </div>

        <!-- Navigation Links -->
        <ul>
            @if(auth()->check() && auth()->user()->role === UserRole::DRIVER->value)
                <li><a href="{{ route('bookings.pending') }}">Pending Bookings</a></li>
                <li><a href="{{ route('driverscompleted.show') }}">My Completed Bookings</a></li>
            @else
                <li><a href="{{ route('destination.index') }}">Destinations</a></li>
                <li><a href="{{ route('hotels.index') }}">Hotels</a></li>
                @if(auth()->check() && auth()->user()->role === UserRole::USER->value)
                <li><a href="{{ route('flight.show') }}">Flights</a></li>
                @endif
                <li><a href="{{route('transport.index')}}">Transport</a></li>
                <li><a href="{{route('activities.index')}}">Activities</a></li>
                @if(auth()->check() && auth()->user()->role === UserRole::USER->value)
                <li>
                    <div class="hidden sm:flex sm:items-center sm:ms-6">
                    <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button>
                            <div style="font-weight:bold">Trips</div>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('trip.view')">
                            {{ ('Create Trip') }}
                        </x-dropdown-link>
                        <x-dropdown-link :href="route('trips.index')">
                            {{ ('My Trips') }}
                        </x-dropdown-link>
                    </x-slot>
                </x-dropdown>
            </div>
         </li>
         @endif
     

                @if(auth()->check() && auth()->user()->role === UserRole::ADMIN->value)
                    <li><a href="{{route('drivers.index')}}">Drivers</a></li>

                    <!-- Admin Reservations Dropdown -->
                    <li x-data="{ openDropdown: false }" class="relative">
                        <button @click="openDropdown = !openDropdown" class="px-3 py-2 hover:bg-gray-100 rounded">
                            Reservations
                        </button>
                        <ul x-show="openDropdown" @click.outside="openDropdown = false"
                            class="absolute mt-1 bg-white border rounded shadow-md">
                            <li><a href="{{ route('reservations.index') }}" class="block px-4 py-2 hover:bg-gray-100">Hotels</a>
                            </li>
                            <li><a href="{{ route('transport.reservations.index') }}"
                                    class="block px-4 py-2 hover:bg-gray-100">Transport</a></li>
                        </ul>
                    </li>
                @endif
            @endif
        </ul>

        <!-- Settings Dropdown -->
        <div class="hidden sm:flex sm:items-center sm:ms-6">
            <x-dropdown align="right" width="48">
                <x-slot name="trigger">
                    <button>
                        <div>{{ Auth::user()->name }}</div>
                    </button>
                </x-slot>

                <x-slot name="content">
                    <x-dropdown-link :href="route('profile.edit')">Profile</x-dropdown-link>

                    @if(Auth::check() && auth()->user()->role === UserRole::USER->value)
                        <!-- User-specific reservations via username dropdown -->
                        <x-dropdown-link :href="route('reservations.index')">Hotel Reservations</x-dropdown-link>
                        <x-dropdown-link :href="route('transport.reservations.index')">Transport
                            Reservations</x-dropdown-link>
                        <x-dropdown-link :href="route('favorites.show')">Show Favorite</x-dropdown-link>
                    @endif

                    <!-- Logout -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-dropdown-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                            Log Out
                        </x-dropdown-link>
                    </form>
                </x-slot>
            </x-dropdown>
            @if(auth()->check() && auth()->user()->role === UserRole::USER->value || 
            auth()->check() && auth()->user()->role === UserRole::DRIVER->value )
<!-- Notifications -->
<div x-data="{ notifOpen: false }" class="relative ml-4">
    <!-- زر الجرس -->
    <button
        @click="notifOpen = !notifOpen"
        class="relative p-2 rounded-full hover:bg-gray-100 focus:outline-none"
    >
        <!-- أيقونة الجرس -->
        <svg class="h-6 w-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>

        <!-- عداد الإشعارات غير المقروءة -->
        @php
            $unread = auth()->user()->unreadNotifications->count();
        @endphp

        @if($unread > 0)
            <span
                class="absolute top-0 right-0 inline-flex items-center justify-center
                       px-2 py-1 text-xs font-bold leading-none text-white
                       bg-red-600 rounded-full">
                {{ $unread }}
            </span>
        @endif
    </button>

    <!-- Dropdown الإشعارات -->
    <div
        x-show="notifOpen"
        x-transition
        x-cloak
        @click.away="notifOpen = false"
        style="display: none;"
        class="absolute right-0 mt-2 w-80 bg-white shadow-lg rounded-lg
               z-50 overflow-hidden"
    >
        <div class="p-4 border-b font-bold text-gray-700">
            Notifications
        </div>

        <div class="max-h-64 overflow-y-auto">
            @forelse(auth()->user()->unreadNotifications as $notification)
                <a
                    href="{{ route('notifications.show', $notification->id) }}"
                    @click="notifOpen = false"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                >
                    {{ $notification->data['message'] ?? 'New notification' }}
                    <span class="text-gray-400 text-xs block">
                        {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                    </span>
                </a>
            @empty
                <p class="px-4 py-2 text-sm text-gray-500">
                    No new notifications.
                </p>
            @endforelse
        </div>

        <div class="p-2 border-t text-center">
            <a
                href="{{ route('notifications.index') }}"
                class="text-blue-600 text-sm hover:underline"
            >
                View All
            </a>
        </div>
    </div>
</div>

@endif


        </div>

        <!-- Hamburger for mobile -->
        <div class="-me-2 flex items-center sm:hidden">
            <button @click="open = ! open"
                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100">
            </button>
            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round"
                    stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                    stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            </button>
        </div>
    </nav>
</div>
