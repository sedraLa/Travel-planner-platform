<x-app-layout>
    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/transport.css') }}">
    <link rel="stylesheet" href="{{ asset('css/vehicles.css') }}">
@endpush
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Favorites') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    <!-- Favorite Destinations Section -->
                    <div class="mb-12">
                        <h3 class="text-2xl font-bold text-gray-800 mb-6 border-l-4 border-blue-500 pl-4">
                            Favorite Destinations
                        </h3>

                        @if($destinations->isNotEmpty())
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                                @foreach ($destinations as $destination)
                                    <a href="{{ route('destination.show', $destination->id) }}"
                                        class="bg-gray-50 rounded-lg shadow-md overflow-hidden transform hover:scale-105 transition-transform duration-300 block">

                                        @php
                                            $primaryImage = $destination->images->where('is_primary', true)->first();
                                        @endphp

                                        <img src="{{ $primaryImage ? asset('storage/' . $primaryImage->image_url) : 'https://via.placeholder.com/400x250.png/007bff/ffffff?text=Destination' }}"
                                            alt="{{ $destination->name }}" class="w-full h-48 object-cover">

                                        <div class="p-4">
                                            <h4 class="text-lg font-semibold text-gray-900">{{ $destination->name }}</h4>
                                            <p class="text-sm text-gray-600 mt-1">{{ $destination->city ?? 'Explore now' }}</p>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 italic">You haven't added any favorite destinations yet.</p>
                        @endif
                    </div>

                    <!-- Favorite Hotels Section -->
                    <div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-6 border-l-4 border-blue-500 pl-4">
                            Favorite Hotels
                        </h3>
                        @if($hotels->isNotEmpty())
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                                @foreach ($hotels as $hotel)
                                    <a href="{{ route('hotel.show', $hotel->id) }}"
                                        class="bg-gray-50 rounded-lg shadow-md overflow-hidden transform hover:scale-105 transition-transform duration-300 block">

                                        @php
                                            $primaryImage = $hotel->images->where('is_primary', true)->first();
                                        @endphp

                                        <img src="{{ $primaryImage ? asset('storage/' . $primaryImage->image_url) : 'https://via.placeholder.com/400x250.png/00529b/ffffff?text=Hotel' }}"
                                            alt="{{ $hotel->name }}" class="w-full h-48 object-cover">

                                        <div class="p-4">
                                            <h4 class="text-lg font-semibold text-gray-900">{{ $hotel->name }}</h4>
                                            <p class="text-sm text-gray-600 mt-1">{{ $hotel->city ?? 'Book now' }}</p>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 italic">You haven't added any favorite hotels yet.</p>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
