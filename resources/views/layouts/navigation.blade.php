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
        <li><a href="{{ route('driverscompleted.show')}}">
               My Completed Bookings  </a> </li>

        @else

            <li><a href="{{ route('destination.index') }}">Destinations</a></li>
            <li><a href="{{ route('hotels.index') }}">Hotels</a></li>
            <li><a href="{{ route('flight.show') }}">Flights</a></li>
            <li><a href="{{route('transport.index')}}">Transport</a></li>
            <li><a href="{{route('activities.index')}}">Activities</a></li>
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
                        {{ ('Create a trip') }}
                    </x-dropdown-link>
                </x-slot>
            </x-dropdown>
        </div>
     </li>

            @if (auth()->check() && auth()->user()->role === UserRole::ADMIN->value)
                <li><a href="{{route('drivers.index')}}">Drivers</a></li>
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
                    <x-dropdown-link :href="route('profile.edit')">
                        {{ ('Profile') }}
                    </x-dropdown-link>
                    <x-dropdown-link :href="route('reservations.index')">
                        {{ __('Show reservation') }}
                    </x-dropdown-link>

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <x-dropdown-link :href="route('logout')" onclick="event.preventDefault();
                                            this.closest('form').submit();">
                            {{ ('Log Out') }}
                        </x-dropdown-link>
                    </form>


                    @if(Auth::check() && Auth::user()->role == 'user')
                        <x-dropdown-link :href="route('favorites.show')">
                            {{ __('Show Favorite') }}
                        </x-dropdown-link>
                    @endif
                </x-slot>
            </x-dropdown>
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



            
        </div>
</div>



<!-- Hamburger -->
<div class="-me-2 flex items-center sm:hidden">
    <button @click="open = ! open"
        class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
            <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round"
                stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>
</div>
</div>
</div>

<!-- Responsive Navigation Menu -->
<div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
    <div class="pt-2 pb-3 space-y-1">
        <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
            {{ ('Dashboard') }}
        </x-responsive-nav-link>
    </div>

    <!-- Responsive Settings Options -->
    <div class="pt-4 pb-1 border-t border-gray-200">
        <div class="px-4">
            <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
            <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
        </div>

        <div class="mt-3 space-y-1">
            <x-responsive-nav-link :href="route('profile.edit')">
                {{ ('Profile') }}
            </x-responsive-nav-link>

            <!-- Authentication -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault();
                                        this.closest('form').submit();">
                    {{ __('Log Out') }}
                </x-responsive-nav-link>
            </form>
        </div>
    </div>
</div>
</nav>
