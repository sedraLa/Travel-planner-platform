@push('styles')
<link rel="stylesheet" href="{{asset('css/vehicles.css')}}">
<link rel="stylesheet" href="{{ asset('css/transport.css') }}">
<link rel="stylesheet" href="{{asset('css/driverDetails.css')}}">
@endpush
<x-app-layout>

<div class="main-container">
<div class="head">
<a href="{{route('drivers.request.index')}}">Back</a>
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
                <span class="badge pending">Pending</span>
           
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
   </div>
</div>

</x-app-layout>
