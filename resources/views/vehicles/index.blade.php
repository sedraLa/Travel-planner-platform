@php use App\Enums\UserRole; @endphp
<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/cardetails.css') }}">
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.css" />
        <style>
            #route-map {
                height: 400px;
                border-radius: 15px;
                overflow: hidden;
                margin-top: 10px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
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

    <div class="main-container">
        <header>
            <a href="{{route('transport.index')}}"><button class="back">Back</button></a>
            <div class="head">
                <h1>Available cars <span id="route-summary" class="route-badge">Loading...</span></h1>
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
            @forelse($availableVehicles as $vehicle)
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
                            <h4>Driver Name : <span class="driver-name">{{$vehicle->driver ? $vehicle->driver->user->name : 'No driver assigned'}}</span></h4>
                            <div class="align">
                                <img src="{{asset('images/icons/phone-solid-full.svg')}}" class="icon">
                                <p>{{$vehicle->driver ? $vehicle->driver->user->phone_number : 'No driver assigned'}}</p>
                            </div>
                        </div>
        
                        <div class="price-section">
                            <div class="left">
                                <h4>Base Price : {{$vehicle->base_price}}$</h4>
                                <p>+ ${{$vehicle->price_per_km}}/km</p>
                            </div>
                        </div>
        
                        <form action="{{ route('vehicle.reservation', $vehicle->id) }}" method="GET">
                            <input type="hidden" name="pickup_location" value="{{ $pickup_location }}">
                            <input type="hidden" name="dropoff_location" value="{{ $dropoff_location }}">
                            <input type="hidden" name="pickup_datetime" value="{{ $pickup_datetime }}">
                            <input type="hidden" name="passengers" value="{{ $passengers }}">
                            <input type="hidden" name="distance" class="distance-input">
                            <input type="hidden" name="duration" class="duration-input">
                            <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">
                            <div class="book-car">
                            <button type="submit">Reserve</button>
                            </div>
                        </form>
                    </div>
                </div>
            @empty
                <p style="text-align:center;font-size:20px;margin-top:40px;">
                    🚘 No vehicles found for your selection
                </p>
            @endforelse
        </div>
        
    </div>

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

                    var control = L.Routing.control({
                        waypoints: [
                            L.latLng(pickup.latitude, pickup.longitude),
                            L.latLng(dropoff.latitude, dropoff.longitude)
                        ],
                        routeWhileDragging: false,
                        showAlternatives: false,
                        createMarker: function (i, wp) { return L.marker(wp.latLng, { draggable: false }); }
                    }).addTo(map);

                    control.on('routesfound', function (e) {
                        var route = e.routes[0];
                        var distance = parseFloat((route.summary.totalDistance / 1000).toFixed(2));
                        var duration = parseFloat((route.summary.totalTime / 60).toFixed(1));

                        document.getElementById('route-summary').innerText = `${distance} km | ${duration} min`;
                        document.getElementById('trip-distance').innerText = `Distance: ${distance} km`;
                        document.getElementById('trip-duration').innerText = `Duration: ${duration} min`;

                        // fill hidden fields
                        document.querySelectorAll('.distance-input').forEach(el => el.value = distance);
                        document.querySelectorAll('.duration-input').forEach(el => el.value = duration);
                    });
                } else {
                    document.getElementById('route-map').innerHTML = "<p style='text-align:center;padding:20px;'>Map data not available</p>";
                }
            });
        </script>
    @endpush
</x-app-layout>