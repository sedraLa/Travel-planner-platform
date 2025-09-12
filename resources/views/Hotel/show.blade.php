@php use App\Enums\UserRole; @endphp

<x-details-layout>
    {{-- Styles --}}
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/details.css') }}">
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <style>
            #hotel-map {
                height: 400px;
                margin-top: 20px;
                border-radius: 20px;
                overflow: hidden;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            }
        </style>
    @endpush

    {{-- Scripts --}}
    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                var lat = @json($coords['latitude'] ?? null);
                var lon = @json($coords['longitude'] ?? null);
                console.log("Latitude:", lat, "Longitude:", lon);

                if (lat && lon) {
                    var map = L.map('hotel-map').setView([lat, lon], 13);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '&copy; OpenStreetMap contributors'
                    }).addTo(map);

                    L.marker([lat, lon]).addTo(map)
                        .bindPopup("{{ addslashes($hotel->name) }}")
                        .openPopup();
                } else {
                    document.getElementById('hotel-map').innerHTML = "<p style='padding:20px;text-align:center;'>Map data not available</p>";
                }
            });
        </script>
    @endpush

    {{-- Main Wrapper --}}
    <div class="main-wrapper">
        <div class="hero-background"
            style="background-image: url('{{ $primaryImage ? asset('storage/' . $primaryImage->image_url) : '' }}');">
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

        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded text-sm">
                @foreach ($errors->all() as $error)
                    <div class="mb-1">• {{ $error }}</div>
                @endforeach
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
                <form action="{{ route('hotels.destroy', $hotel->id) }}" method="POST"
                    onsubmit="return confirm('Are you sure you want to delete this hotel?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded shadow transition duration-200">
                        Delete
                    </button>
                </form>
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

            <div class="address">
                <h1>Address</h1>
                <p>{{ $hotel->address }}</p>
                {{-- Map --}}
                <div id="hotel-map"></div>
            </div>


            <div class="description">
                <h1>Hotel Description</h1>
                <p>{{ $hotel->description }}</p>
            </div>




            <div class="city">
                <h1>City</h1>
                <p>{{ $hotel->destination->city }}</p>
            </div>

            <div class="country">
                <h1>Country</h1>
                <p>{{ $hotel->destination->country }}</p>
            </div>

            <div class="price">
                <h1>Room Price</h1>
                <p>${{ number_format($hotel->price_per_night, 2) }} per night</p>
            </div>

            <div class="rating">
                <h1>Global Rating</h1>
                <p>{{ $hotel->global_rating }} / 5</p>
            </div>

            @if(Auth::check() && Auth::user()->role == 'user')
                <div class="booking">
                    <h1>Book This Hotel</h1>
                    <a href="{{ route('reservations.form', ['id' => $hotel->id]) }}"
                        class="text-white bg-green-600 hover:bg-green-700 font-semibold py-2 px-4 rounded shadow transition duration-200">
                        Book Now
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-details-layout>