@php use App\Enums\UserRole; @endphp
<x-app-layout>
@push('styles')
<link rel="stylesheet" href="{{ asset('css/cardetails.css') }}">
@endpush

<div class="main-container">
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
    <header>
        <a href="{{route('transport.index')}}"><button class="back">Back</button></a>
        <div class="head">
            <h1>Available cars</h1>
            
            <p>Manage Vehicles For This Transport Service</p>
        </div>
    </header>


    <div class="cards">
        @forelse($Vehicles as $vehicle)
            <div class="card">
                <div class="car-image">
                    <img src="{{ asset('storage/' . $vehicle->image) }}" alt="car image" class="car-img">
                </div>
                <div class="details">
                    <div class="top-section">
                        <div class="main-info">
                            <h2>{{ $vehicle->car_model }}</h2>
                            <div class="align">
                                <img src="{{ asset('images/icons/user-group-solid-full.svg') }}" class="icon">
                                <p>Up to {{ $vehicle->max_passengers }} passengers / {{ $vehicle->plate_number }}</p>
                            </div>
                        </div>
                        <div class="category">
                            <span>{{ $vehicle->category }}</span>
                        </div>
                    </div>
    
                    <div class="driver-section">
                        <h4>Driver Name : <span class="driver-name">{{ $vehicle->driver_name }}</span></h4>
                        <div class="align">
                            <img src="{{ asset('images/icons/phone-solid-full.svg') }}" class="icon">
                            <p>{{ $vehicle->driver_contact }}</p>
                        </div>
                    </div>
    
                    <div class="price-section">
                        <div class="left">
                            <h4>Base Price : {{ $vehicle->base_price }}$</h4>
                            <p>+ ${{ $vehicle->price_per_km }}/km</p>
                        </div>
                        <div class="right"></div>
                    </div>
    
                    <div class="manage-btn">
                        <a href="{{ route('admin.vehicles.edit', $vehicle->id) }}">
                            <button class="order-btn edit-btn">Edit Vehicle</button>
                        </a>
    
                        <form action="{{ route('admin.vehicles.destroy', $vehicle->id) }}" method="POST" style="display:inline-block"
                              onsubmit="return confirm('Are you sure you want to delete this vehicle?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="order-btn delete-btn">Delete Vehicle</button>
                        </form>
                    </div>
    
                </div>
            </div>
        @empty
            <p style="color:black; font-size:18px; text-align:center; margin-top:50px;">
                🚗 No vehicles available for this transport service yet.
            </p>
        @endforelse
    </div>
    
</div>


</x-app-layout>
