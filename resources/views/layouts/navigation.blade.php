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
                <li><a href="{{ route('flight.show') }}">Flights</a></li>
                <li><a href="{{route('transport.index')}}">Transport</a></li>

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