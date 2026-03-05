@push('styles')
<link rel="stylesheet" href="{{asset('css/vehicles.css')}}">
<link rel="stylesheet" href="{{ asset('css/transport.css') }}">
<link rel="stylesheet" href="{{asset('css/driverDetails.css')}}">
@endpush
<x-app-layout>

<div class="main-container">
<div class="head">
<a href="{{route('drivers.approved.index')}}">Back</a>
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
            <button onclick="document.getElementById('shift-modal').classList.remove('hidden')"
        class="schedule-btn">
    Working Schedule
</button>
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



 <div class="stat">
   <img class="stat-icon" src="{{ asset('images/icons/icons8-category-50.png') }}" alt="Plate Icon">
      <div>
         <h5>License image</h5>

              <button onclick="document.getElementById('license-modal-{{ $driver->id }}').classList.remove('hidden')"
                class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm font-medium">
                  View License
              </button>

              <div id="license-modal-{{ $driver->id }}"
               class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">

                  {{-- This is the main container for the modal content --}}
                  <div class="license-modal-container">

                     {{-- Close Button --}}
                         <button onclick="document.getElementById('license-modal-{{ $driver->id }}').classList.add('hidden')"
                           class="license-modal-close-btn">
                               &times;
                         </button>

                     {{-- License Image --}}
                         @if($driver->license_image)
                             <img src="{{ asset('storage/' . $driver->license_image) }}"
                             alt="License Photo"
                            class="license-modal-image">
                         @else
                        <p class="text-center text-gray-500">No license photo available</p>
                         @endif

                   </div>
              </div>
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
                    <h6>{{ $vehicle->car_model?? 'no car model' }}</h6>
                </div>

                <div class="stat">
                    <h5>Plate Number</h5>
                    <h6>{{ $vehicle->plate_number?? 'no plate number' }}</h6>
                </div>

                <div class="stat">
                    <h5>Category</h5>
                    <h6>{{ $vehicle->category?? 'no category' }}</h6>
                </div>

                <div class="stat">
                    <h5>Max Passengers</h5>
                    <h6>{{ $vehicle->max_passengers?? 'no max passengers' }}</h6>
                </div>

                <div class="stat">
                    <h5>Base Price</h5>
                    <h6>${{ number_format($vehicle->base_price ?? 0, 2) }}</h6>
                </div>

                <div class="stat">
                    <h5>Price / KM</h5>
                    <h6>${{ number_format($vehicle->price_per_km ?? 0, 2) }}</h6>
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
<!-- SHIFT MODAL -->
<div id="shift-modal"
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">

    <div class="shift-modal-container">

        <button onclick="document.getElementById('shift-modal').classList.add('hidden')"
                class="shift-close-btn">
            &times;
        </button>

        <h3 class="shift-title">Driver Working Schedule</h3>

        <table class="shift-table">
            <thead>
                <tr>
                    <th>Shift Name</th>
                    <th>Time</th>
                    <th>Days</th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td>Morning Shift</td>
                    <td>08:00 - 16:00</td>
                    <td>Mon - Fri</td>
                </tr>

                <tr>
                    <td>Evening Shift</td>
                    <td>16:00 - 00:00</td>
                    <td>Mon - Fri</td>
                </tr>

                <tr>
                    <td>Weekend Shift</td>
                    <td>10:00 - 18:00</td>
                    <td>Sat - Sun</td>
                </tr>
            </tbody>
        </table>

    </div>
</div>

</x-app-layout>
