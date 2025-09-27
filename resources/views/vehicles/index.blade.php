@php use App\Enums\UserRole; @endphp
<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/cardetails.css') }}">
    @endpush

    <!--leaflet map libraries-->
    @push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.css" />
    <style>
        #route-map {
            height: 400px;
            border-radius: 15px;
            overflow: hidden;
            margin-top: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .route-badge {
            background: #007bff;
            color: #fff;
            font-size: 14px;
            padding: 4px 10px;
            border-radius: 12px;
            margin-left: 10px;
        }
    </style>
    @endpush

    @push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var pickup = @json($pickupCoords);
            var dropoff = @json($dropoffCoords);

            if (pickup && dropoff) {
                var map = L.map('route-map').setView([pickup.latitude, pickup.longitude], 13);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(map);

                // Add route
                var control = L.Routing.control({
                    waypoints: [
                        L.latLng(pickup.latitude, pickup.longitude),
                        L.latLng(dropoff.latitude, dropoff.longitude)
                    ],
                    routeWhileDragging: false,
                    showAlternatives: false,
                    createMarker: function(i, wp) {
                        return L.marker(wp.latLng, { draggable: false });
                    }
                }).addTo(map);

                // get distance and duration
                control.on('routesfound', function(e) {
                    var route = e.routes[0];
                    var distance = (route.summary.totalDistance / 1000).toFixed(2); // km
                    var duration = (route.summary.totalTime / 60).toFixed(1); // min

                    // update header
                    document.getElementById('route-summary').innerHTML =
                        `${distance} km | ${duration} min`;

                    // update Trip overview
                    document.getElementById('trip-distance').innerHTML =
                        `Distance: ${distance} km`;
                    document.getElementById('trip-duration').innerHTML =
                        `Duration: ${duration} min`;
                });

            } else {
                document.getElementById('route-map').innerHTML = "<p style='text-align:center;padding:20px;'>Map data not available</p>";
            }
        });
    </script>
    @endpush

    <div class="main-container">
        <header>
            <a href="{{route('transport.index')}}">
                <button class="back">Back</button>
            </a>
            <div class="head">
                <h1>
                    Available cars
                    <span id="route-summary" class="route-badge">Loading...</span>
                </h1>
                @if (Auth::user()->role === UserRole::ADMIN->value)
                <p>Manage Vehicles For This Transport Service</p>
                @endif
                <p>{{$pickup_datetime}}</p>
            </div>
        </header>

        <div class="middle-section">
            <div class="overview">
                <div class="trip-overview">
                    <div class="align">
                        <img class="icon" src="{{asset('images/icons/car-side-solid-full.svg')}}">
                        <h2>Trip overview</h2>
                    </div>

                    <ul class="trip-list">
                        <li>Pickup Location: {{$pickup_location}}</li>
                        <li>Destination Location: {{$dropoff_location}}</li>
                        <li>Passengers: {{$passengers}}</li>
                        <li>Date & Time: {{$pickup_datetime}}</li>
                        <li id="trip-distance">Distance: Loading...</li>
                        <li id="trip-duration">Duration: Loading...</li>
                    </ul>
                </div>
            </div>

            <div class="map" id="route-map"></div>
        </div>

        <div class="cards">
            @foreach($availableVehicles as $vehicle)
                <div class="card">
                    <div class="car-image">
                        <img src="{{asset('storage/' . $vehicle->image)}}" alt="car image" class="car-img">
                    </div>
                    <div class="details">
                        <div class="top-section">
                            <div class="main-info">
                                <h2>{{$vehicle->car_model}}</h2>
                                <div class="align">
                                    <img src="{{asset('images/icons/user-group-solid-full.svg')}}" class="icon">
                                    <p>Up to {{$vehicle->max_passengers}} passengers / {{$vehicle->plate_number}}</p>
                                </div>
                            </div>
                            <div class="category">
                                <span>{{$vehicle->category}}</span>
                            </div>
                        </div>

                        <div class="driver-section">
                            <h4>Driver Name : <span class="driver-name">{{$vehicle->driver_name}}</span></h4>
                            <div class="align">
                                <img src="{{asset('images/icons/phone-solid-full.svg')}}" class="icon">
                                <p>{{$vehicle->driver_contact}}</p>
                            </div>
                        </div>

                        <div class="price-section">
                            <div class="left">
                                <h4>Base Price : {{$vehicle->base_price}}$</h4>
                                <p>+ ${{$vehicle->price_per_km}}/km</p>
                            </div>
                            <div class="right">
                                <p>Final price calculated on completion</p>
                            </div>
                        </div>

                        <a href="{{ route('vehicle.reservation', [
                            'id' => $vehicle->id,
                            'pickup_location' => $pickup_location,
                            'dropoff_location' => $dropoff_location,
                            'pickup_datetime' => $pickup_datetime,
                            'passengers' => $passengers
                        ]) }}">
                            Reserve
                        </a>

                        <a href="{{ route('vehicle.edit', $vehicle->id) }}" class="edit-btn">Edit</a>

                        <form action="{{ route('vehicle.destroy', $vehicle->id) }}" method="POST"
                            onsubmit="return confirm('Are you sure you want to delete this vehicle?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="delete-btn">Delete</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
