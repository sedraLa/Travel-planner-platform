@php use App\Enums\UserRole;@endphp
<x-app-layout>
    @push('styles')
    <link rel="stylesheet" href="{{asset('css/destinations.css')}}">
    @endpush

    {{--body content--}}

    <div class="main-wrapper">
        @if (Auth::user()->role === UserRole::ADMIN->value)
            <!-- Create Destination Button -->
            <div class="flex justify-end mb-4 px-6 pt-6">
                <a href="{{ route('destinations.create') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded shadow transition duration-200">
                    + Add New Destination
                </a>
            </div>
    
            @if (session('success'))
                <div class="mb-4 px-4 py-3 bg-green-100 text-green-800 rounded">
                    {{ session('success') }}
                </div>
            @endif
        @endif
    
        <!-- Hero Background (should be visible to all users) -->
        <div class="hero-background destinations-page"></div>
    
        {{-- Search Form (also visible to all users) --}}
        <form class="search-form" method="GET" action="{{route('destination.index')}}">
            <h1>Search your next destination</h1>
            <div class="search-container">
                <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                </svg>
                <input type="search" id="default-search" name="search" class="search-input" placeholder="Search destinations..." required />
                <button type="submit" class="search-button">Search</button>
            </div>
        </form>
    
        {{-- Cards Section --}}
        <div class='cards'>
            @forelse ($destinations as $destination)
            <div class="card">
                <a href="{{ route('destination.show', $destination->id) }}">
                    <div class="card-img">
                        <img src="{{ asset('storage/' . optional($destination->images->where('is_primary', true)->first())->image_url) }}" alt="Destination Image">

                    </div>
                    <h5>{{ $destination->name }}</h5>
                    <p class="overview">{{ Str::limit($destination->description, 80) }}</p>
                </a>
            </div>
            @empty
                <p style="text-align:center;">No destinations found.</p>
            @endforelse
        </div>
    </div>
        
</x-app-layout>