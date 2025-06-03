@php use App\Enums\UserRole; @endphp

<x-details-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/details.css') }}">
    @endpush

    {{-- Main Wrapper --}}

    <div class="main-wrapper">
        <div class="hero-background" style="background-image: url('{{ $primaryImage ? asset('storage/' . $primaryImage->image_url) : '' }}');">
            <div class="headings" style="font-size:32px;">
                <h1>{{ $hotel->name }}</h1>
                <h3>{{ $hotel->city }}</h3>
            </div>
        </div>
    </div>

    {{-- Details Section --}}
    <div class="details">
        @if (session('success'))
        <div class="mb-4 px-4 py-3 bg-green-100 text-green-800 rounded">
            {{ session('success') }}
        </div>
    @endif
        <header>Explore everything about this hotel</header>

        @if (Auth::user()->role === UserRole::ADMIN->value)
            <!-- Admin Controls -->
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Edit Hotel') }}
                </h2>
               <a href="{{ route('hotels.edit', $hotel->id) }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded shadow transition duration-200">
                    Edit
                </a>
{{--
                <form action="{{ route('hotels.destroy', $hotel->id) }}" method="POST"
                      onsubmit="return confirm('Are you sure you want to delete this hotel?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded shadow transition duration-200">
                        Delete
                    </button>
                </form>--}}
            </div>
        @endif

        {{-- Other Hotel Images --}}
        <div class="cards">
            @foreach($hotel->images as $image)
                @if(!$image->is_primary)
                    <div class="card">
                        <img src="{{ asset('storage/' . $image->image_url) }}" alt="Hotel Image">
                    </div>
                @endif
            @endforeach
        </div>

        {{-- Hotel Info --}}
        <div class="container">

            <div class="description">
                <h1>Hotel Description</h1>
                <p>{{ $hotel->description }}</p>
            </div>

            <div class="address">
                <h1>Adress</h1>
                <p>{{ $hotel->address}}</p>
            </div>

            <div class="city">
                <h1>City</h1>
                <p>{{ $hotel->destination->city}} </p>
            </div>

            <div class="country">
                <h1>Country</h1>
                <p>{{ $hotel->destination->country}} </p>
            </div>

            <div class="price">
                <h1>Room Price</h1>
                <p>${{ number_format($hotel->price_per_night, 2) }} per night</p>
            </div>

            <div class="rating">
                <h1>Global Rating</h1>
                <p>{{ $hotel->global_rating }} / 5</p>
            </div>


{{-- <div class="booking">
                <h1>Book This Hotel</h1>
                <a href="{{ route('booking.create', ['hotel_id' => $hotel->id]) }}"
                   class="text-white bg-green-600 hover:bg-green-700 font-semibold py-2 px-4 rounded shadow transition duration-200">
                    Book Now
                </a>
            </div>--}}


        </div>
    </div>
</x-details-layout>
