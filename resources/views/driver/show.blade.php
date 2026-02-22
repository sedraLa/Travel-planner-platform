@push('styles')
<link rel="stylesheet" href="{{asset('css/vehicles.css')}}">
<link rel="stylesheet" href="{{ asset('css/transport.css') }}">
<link rel="stylesheet" href="{{asset('css/driverDetails.css')}}">
@endpush
<x-app-layout>

<div class="main-container">
<div class="heading">
<a href="{{route('drivers.index')}}">Back</a>
<h1>Driver Details</h1>
</div>
<div class="left-section">
<div class="top-section">
<img src="{{ $driver && $driver->personal_image ? asset('storage/' . $driver->personal_image) : asset('images/ian-dooley-d1UPkiFd04A-unsplash.jpg') }}" alt="personal image">
<h2>{{$driver?->user?->name}} {{$driver?->user?->last_name}}</h2>
<div class="status">
<h4> {{$driver?->status}} </h4> <!--availability-->
<h4> {{$driver?->status}} </h4>  <!--approved-->
</div>
<span id="rating">⭐ 4.9 rating</span>
</div>
<div class="bottom-section">
<h3>Performance</h3>
<div class="bookings">
 <div class ="pending">
            <img class="icon" src="{{asset('images/icons/icons8-pending-50.png')}}">
             <h3>Pending</h3>
            <div class="count">
            <span id="pending-total">{{ $pendingBookings }}</span>
            </div>
            </div>
        </div>

   <div class ="completed">
            <img class="icon" src="{{asset('images/icons/icons8-checkmark-50.png')}}">
             <h3>completed</h3>
            <div class="count">
            <span id="completed-total">{{ $completedBookings }}</span>
            </div>
            </div>
        </div>

        <div class ="canceled">
            <img class="icon" src="{{asset('images/icons/icons8-cancel-50.png')}}">
            <h3>Canceled</h3>
            <div class="count">
            <span id="canceled-total">{{ $canceledBookings }}</span>
            </div>
            </div>
        </div>

        <div class ="earning">
            <img class="icon" src="{{asset('images/icons/icons8-earning-50.png')}}">
            <h3>Earnings</h3>
            <div class="count">
            <span id="earning-total">2 <!--number of pending booings--> </span>
            <span id="earning-total"> 2</span>
            </div>
            </div>
        </div>
</div>
</div>
</div>
<div class="right-section">
<h3>Personal Information</h3>
<div class="info">
<div class="stat">
  <img class="stat-icon" src="{{ asset('images/icons/icons8-licence-plate-50.png') }}" alt="Plate Icon">
    <div>
       <h5>Email</h5>
          <h6>{{ $driver?->user?->email ?? 'N/A' }}</h6>
     </div>
 </div>

<div class="stat">
   <img class="stat-icon" src="{{ asset('images/icons/icons8-licence-plate-50.png') }}" alt="Plate Icon">
      <div>
         <h5>Contact</h5>
             <h6>{{ $driver?->user?->phone_number ?? 'N/A' }}</h6>
      </div>
 </div>

 <div class="stat">
   <img class="stat-icon" src="{{ asset('images/icons/icons8-licence-plate-50.png') }}" alt="Plate Icon">
      <div>
         <h5>Country</h5>
             <h6>{{ $driver?->user?->country ?? 'N/A' }}</h6>
      </div>
 </div>

 <div class="stat">
   <img class="stat-icon" src="{{ asset('images/icons/icons8-licence-plate-50.png') }}" alt="Plate Icon">
      <div>
         <h5>Address</h5>
             <h6>{{ $driver?->address ?? 'N/A' }}</h6>
      </div>
 </div>

 <div class="stat">
   <img class="stat-icon" src="{{ asset('images/icons/icons8-licence-plate-50.png') }}" alt="Plate Icon">
      <div>
         <h5>Experience</h5>
             <h6>{{ $driver?->experience ?? 'N/A' }}</h6>
      </div>
 </div>

 <div class="stat">
   <img class="stat-icon" src="{{ asset('images/icons/icons8-licence-plate-50.png') }}" alt="Plate Icon">
      <div>
         <h5>License Category</h5>
             <h6>{{ $driver?->license_category ?? 'N/A' }}</h6>
      </div>
 </div>

 <div class="stat">
   <img class="stat-icon" src="{{ asset('images/icons/icons8-licence-plate-50.png') }}" alt="Plate Icon">
      <div>
         <h5>Date Of Hire</h5>
             <h6>{{ $driver?->date_of_hire ?? 'N/A' }}</h6>
      </div>
 </div>
{{--
 <div class="stat">
   <img class="stat-icon" src="{{ asset('images/icons/icons8-licence-plate-50.png') }}" alt="Plate Icon">
      <div>
         <h5>Contact</h5>
             <h6>{{ $driver?->date_of_hire ?? 'N/A' }}</h6>
      </div>
 </div>
--}}




</div>

<div class="vehicle-section">
    <div class="vehicle-header">
        <h3>Assigned Vehicle</h3>

        @if($vehicle)
            <a href="{{ route('admin.vehicles.edit', $vehicle->id) }}" class="edit-btn">
                ✏️ Edit Vehicle
            </a>
        @endif
    </div>

    @if($vehicle)
        <div class="vehicle-card">

            <div class="vehicle-image">
                <img src="{{ $vehicle->image
                    ? asset('storage/' . $vehicle->image)
                    : asset('images/default-car.png') }}"
                    alt="Vehicle Image">
            </div>

            <div class="vehicle-info">

                <div class="stat">
                    <h5>Car Model</h5>
                    <h6>{{ $vehicle->car_model }}</h6>
                </div>

                <div class="stat">
                    <h5>Plate Number</h5>
                    <h6>{{ $vehicle->plate_number }}</h6>
                </div>

                <div class="stat">
                    <h5>Category</h5>
                    <h6>{{ $vehicle->category }}</h6>
                </div>

                <div class="stat">
                    <h5>Max Passengers</h5>
                    <h6>{{ $vehicle->max_passengers }}</h6>
                </div>

                <div class="stat">
                    <h5>Base Price</h5>
                    <h6>${{ number_format($vehicle->base_price, 2) }}</h6>
                </div>

                <div class="stat">
                    <h5>Price / KM</h5>
                    <h6>${{ number_format($vehicle->price_per_km, 2) }}</h6>
                </div>

            </div>
        </div>
    @else
        <div class="no-vehicle">
            <img src="{{ asset('images/icons/icons8-car-100.png') }}">
            <p>No vehicle assigned yet</p>
        </div>
    @endif
</div>
</div>
</div>

</x-app-layout>
