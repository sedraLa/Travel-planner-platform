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
                <li><a href="{{ route('destination.index') }}" >Destinations</a></li>
                <li><a href="{{ route('hotels.index') }}" >Hotels</a></li>
                <li><a href="{{ route('flight.show') }}" >Flights</a></li>
                <li><a href="{{route('transport.index')}}">Transport</a></li>
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

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <x-dropdown-link :href="route('logout')" onclick="event.preventDefault();
                                            this.closest('form').submit();">
                            {{ ('Log Out') }}
                        </x-dropdown-link>
                    </form>
                    <x-dropdown-link :href="route('favorites.show')">
                        {{ ('Show Favorite') }}
                    </x-dropdown-link>

                </x-slot>
            </x-dropdown>
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