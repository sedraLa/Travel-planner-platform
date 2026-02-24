@push('styles')
<link rel="stylesheet" href="{{asset('css/vehicles.css')}}">
<link rel="stylesheet" href="{{ asset('css/transport.css') }}">
<link rel="stylesheet" href="{{asset('css/driverDetails.css')}}">
@endpush
<x-app-layout>

<div class="main-container">
<div class="head">
<a href="{{route('drivers.index')}}">Back</a>
<h1>Driver Details</h1>
</div>
<div class="driver-details">
<div class="left-section">
    <div class="top-section">
        <div class="top-header"></div>
        <div class="top-content">
            <img class="personal-image"
                 src="{{ $driver && $driver->personal_image ? asset('storage/' . $driver->personal_image) : asset('images/ian-dooley-d1UPkiFd04A-unsplash.jpg') }}"
                 alt="personal image">
            <h2 class="driver-name">
                {{$driver?->user?->name}} {{$driver?->user?->last_name}}
            </h2>
            <div class="status">
                <span class="badge available">Available</span>
                <span class="badge approved">Approved</span>
            </div>
            <div class="rating">
                ⭐ 4.9 rating
            </div>
        </div>
    </div>

<div class="bottomm-section">
<h3>Performance</h3>
<div class="bookings">
 <div class ="total pending">
    <div class="type">
            <img class="icon" src="{{asset('images/icons/icons8-pending-50.png')}}">
             <h3>Pending</h3>
    </div>
            <div class="count">
            <span id="pending-total">{{ $pendingBookings }}</span>
            </div>
        </div>

   <div class="total completed">
   <div class="type">
            <img class="icon" src="{{asset('images/icons/icons8-checkmark-50.png')}}">
             <h3>completed</h3>
             </div>
            <div class="count">
            <span id="completed-total">{{ $completedBookings }}</span>
            </div>
        </div>

        <div class="total canceled">
            <div class="type">
            <img class="icon" src="{{asset('images/icons/icons8-cancel-50.png')}}">
            <h3>Canceled</h3>
            </div>
            <div class="count">
            <span id="canceled-total">{{ $canceledBookings }}</span>
            </div>
        </div>

        <div class ="total earning">
            <div class="type">
            <img class="icon" src="{{asset('images/icons/icons8-earning-50.png')}}">
            <h3>Earnings</h3>
            </div>
            <div class="count">
            <span id="earning-total">2 <!--number of pending booings--> </span>
            </div>
        </div>
</div>
</div>
</div>
<div class="right-section">
<div class="info">
    <h3>Personal Information</h3>
<div class="stat">
  <img class="stat-icon" src="{{ asset('images/icons/icons8-email-50.png') }}" alt="Plate Icon">
    <div>
       <h5>Email</h5>
          <h6>{{ $driver?->user?->email ?? 'N/A' }}</h6>
     </div>
 </div>

<div class="stat">
   <img class="stat-icon" src="{{ asset('images/icons/icons8-phone-50.png') }}" alt="Plate Icon">
      <div>
         <h5>Contact</h5>
        <h6>{{ $driver?->user?->phone_number ?? 'N/A' }}</h6>
      </div>
 </div>

 <div class="stat">
   <img class="stat-icon" src="{{ asset('images/icons/icons8-around-the-globe-50.png') }}" alt="Plate Icon">
      <div>
         <h5>Country</h5>
             <h6>{{ $driver?->user?->country ?? 'N/A' }}</h6>
      </div>
 </div>

 <div class="stat">
   <img class="stat-icon" src="{{ asset('images/icons/icons8-country-50.png') }}" alt="Plate Icon">
      <div>
         <h5>Address</h5>
             <h6>{{ $driver?->address ?? 'N/A' }}</h6>
      </div>
 </div>

 <div class="stat">
   <img class="stat-icon" src="{{ asset('images/icons/icons8-experience-50.png') }}" alt="Plate Icon">
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
   <img class="stat-icon" src="{{ asset('images/icons/icons8-category-50.png') }}" alt="Plate Icon">
      <div>
         <h5>Date Of Hire</h5>
             <h6>{{ $driver?->date_of_hire ?? 'N/A' }}</h6>
      </div>
 </div>

 <div class="stat">
   <img class="stat-icon" src="{{ asset('images/icons/icons8-24-hours-50.png') }}" alt="Plate Icon">
      <div>
         <h5>Availability</h5>
             <h6>{{ $driver?->date_of_hire ?? 'N/A' }}</h6>
      </div>
 </div>





</div>


<div class="vehicle-section">
    <div class="vehicle-header">
        <h3>Assigned Vehicle</h3>

        @if($vehicle)
            <a href="{{ route('admin.vehicles.edit', $vehicle->id) }}" class="edit-btn">
                 Edit Vehicle
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


</x-app-layout>
