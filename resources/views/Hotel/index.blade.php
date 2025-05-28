@php use App\Enums\UserRole; @endphp

<x-app-layout>
    @push('styles')
   <link rel="stylesheet" href="{{asset('css/destinations.css')}}">
    @endpush

    <div class="main-wrapper">
        @if (Auth::user()->role === UserRole::ADMIN->value)
            <!-- Create Hotel Button -->
            <div class="flex justify-end mb-4 px-6 pt-6">
                <a href="{{ url('hotels.create') }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded shadow transition duration-200">
                    + Add New Hotel
                </a>
            </div>

            @if (session('success'))
                <div class="mb-4 px-4 py-3 bg-green-100 text-green-800 rounded">
                    {{ session('success') }}
                </div>
            @endif
        @endif

        <!-- Hero Background (for all users) -->
        <div class="hero-background hotels-page"></div>

                            <!-- Search Form -->
        <form class="search-form" method="GET" action="{{ route('hotels.index') }}">
            <h1>Find Your Hotel</h1>
            <div class="search-container">
                <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                </svg>
                <input type="search" name="search" class="search-input" placeholder="Search hotels..." required />
                <button type="submit" class="search-button">Search</button>
            </div>
        </form>



        <!-- Hotels Cards -->
        <div class="cards">
            @forelse ($hotels as $hotel)
                <div class="card">
                    <a href="{{ route('hotel.show', $hotel->id) }}">
                        <div class="card-img">
                            <img src="{{ asset('storage/' . optional($hotel->image)->image_url) }}" alt="Hotel Image">
                        </div>
                        <h5>{{ $hotel->name }}</h5>
                        <p class="overview">{{ Str::limit($hotel->location, 80) }}</p>
                    </a>
                </div>
            @empty
                <p style="text-align:center;">No hotels found.</p>
            @endforelse
        </div>
    </div>
</x-app-layout>
