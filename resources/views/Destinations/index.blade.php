@php use App\Enums\UserRole;
@endphp;
<x-app-layout>
    @push('styles')
    <link rel="stylesheet" href="{{asset('css/destinations.css')}}">
    @endpush

    {{--body content--}}

    <div class="main-wrapper">
        <div class="hero-background"></div>
        {{--search form--}}
        <form class="search-form" method="GET" action="{{route('destination.index')}}">
            <h1>Search your next destination</h1>
            <div class="search-container">
                <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                </svg>
                <input type="search" id="default-search" name="search" class="search-input" placeholder="Search destinations..." required />
                <button type="submit" class="search-button">Search</button>

                @if (Auth::user()->role === UserRole::ADMIN->value)
                    <!-- Create Movie Button -->
                    <a href="{{ route('destinations.create') }}"
                        class="ml-4 bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-500">
                        Create
                    </a>
                @endif                     
                              
                

            </div>
        </form>
        {{--cards section--}}
        <section class="cards">
            @forelse ($destinations as $destination)
                <div class="card">
                <div class="card-img">
                <img src="{{ asset('storage/' . optional($destination->primary_image)->image_url) }}" alt="Destination Image">
                </div>
                <h5>{{ $destination->name }}</h5>
                <p class="overview">{{ Str::limit($destination->description, 80) }}</p>
                </div>
            @empty
                <p style="text-align:center;">No destinations found.</p>
            @endforelse
            </section>
    </div>


    
</x-app-layout>