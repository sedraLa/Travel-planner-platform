@push('styles')
<link rel="stylesheet" href="{{asset('css/vehicles.css')}}">
<link rel="stylesheet" href="{{ asset('css/transport.css') }}">
<link rel="stylesheet" href="{{ asset('css/driver_dash.css') }}">
@endpush
<x-app-layout>
    <div class="main">
    <div class="driver_profile">
        <div class="personal_info">
            <img class="personal-photo" src="{{ $driver && $driver->personal_image ? asset('storage/' . $driver->personal_image) : asset('images/ian-dooley-d1UPkiFd04A-unsplash.jpg') }}">
            <div class="personal-details">
                <span>Good day</span>
                <h2>Welcome back, {{ $driver?->user?->name ?? auth()->user()->name }} 👋</h2>
                <p id="rating">⭐ 4.9 rating . {{ $completedBookings }} total trips</p>
            </div>
        </div>
    </div>
    <div class ="bookings-summary">
        <!--pending bookings-->
        <div class ="pending">
            <img class="icon" src="{{asset('images/icons/icons8-pending-50.png')}}">
            <div class="count">
            <h3>PENDING</h3>
            <span id="pending-total">{{ $pendingBookings }}</span>
            </count>
            </div>
        </div>

        <div class ="completed">
            <img class="icon" src="{{asset('images/icons/icons8-checkmark-50.png')}}">
            <div class="count">
            <h3>COMPLETED</h3>
            <span id="completed-total">{{ $completedBookings }}</span>
            </count>
            </div>
        </div>

        <div class ="canceled">
            <img class="icon" src="{{asset('images/icons/icons8-cancel-50.png')}}">
            <div class="count">
            <h3>CANCELED</h3>
            <span id="canceled-total">{{ $canceledBookings }}</span>
            </count>
            </div>
        </div>

        <div class ="earning">
            <img class="icon" src="{{asset('images/icons/icons8-earning-50.png')}}">
            <div class="count">
            <h3>EARNINGS</h3>
            <span id="earning-total">2 <!--number of pending booings--> </span>
            {{--<span id="earning-total">${{ number_format($earnings, 2) }}</span>--}}
            </count>
            </div>
        </div>
    </div>

    <div class="assigned-vehicle">
        <h2 class="vehicle-title">Your Assigned Vehicle</h2>
        <div class="vehicle-info">
           <img class="vehicle-photo" src="{{ $vehicle && $vehicle->image ? asset('storage/' . $vehicle->image) : asset('images/cari-kolipoki-rEmiiyRZi8g-unsplash.jpg') }}" alt="Vehicle Image">

            <div class="vehicle-details">
                <h2 class="vehicle-model">{{ $vehicle->car_model ?? 'No vehicle assigned' }}</h2>
                <span class="category">{{ $vehicle->category ?? 'N/A' }}</span>
                <div class="vehicle-stats">
                    <div class="stat">
                        <img class="stat-icon" src="{{ asset('images/icons/icons8-licence-plate-50.png') }}" alt="Plate Icon">
                        <div>
                            <h5>Plate Number</h5>
                            <h6>{{ $vehicle->plate_number ?? 'N/A' }}</h6>
                        </div>
                    </div>
                    <div class="stat">
                        <img class="stat-icon" src="{{ asset('images/icons/icons8-passengers-50.png') }}" alt="Passengers Icon">
                        <div>
                            <h5>Max Passengers</h5>
                            <h6>{{ $vehicle->max_passengers ?? 'N/A' }}</h6>
                        </div>
                    </div>
                    <div class="stat">
                        <img class="stat-icon" src="{{ asset('images/icons/icons8-price-50.png') }}" alt="Base Price Icon">
                        <div>
                            <h5>Base Price</h5>
                            <h6>${{ isset($vehicle->base_price) ? number_format($vehicle->base_price, 2) : 'N/A' }}</h6>
                        </div>
                    </div>
                    <div class="stat">
                        <img class="stat-icon" src="{{ asset('images/icons/icons8-price-50.png') }}" alt="Price per Km Icon">
                        <div>
                            <h5>Price per Km</h5>
                            <h6>${{ isset($vehicle->price_per_km) ? number_format($vehicle->price_per_km, 2) : 'N/A' }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</x-app-layout>
