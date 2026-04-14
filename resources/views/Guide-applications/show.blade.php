@push('styles')
<link rel="stylesheet" href="{{asset('css/vehicles.css')}}">
<link rel="stylesheet" href="{{ asset('css/transport.css') }}">
<link rel="stylesheet" href="{{asset('css/driverDetails.css')}}">
@endpush
<x-app-layout>

<div class="main-container">
<div class="head">
<a href="{{route('guide-applications.index')}}">Back</a>
<h1>Guide Details</h1>
</div>
<div class="driver-details">
<div class="left-section">
    <div class="top-section">
        <div class="top-header"></div>
        <div class="top-content">
            <img class="personal-image"
                 src="{{ $guide && $guide->personal_image ? asset('storage/' . $guide->personal_image) : asset('images/ian-dooley-d1UPkiFd04A-unsplash.jpg') }}"
                 alt="personal image">
            <h2 class="driver-name">
                {{$guide?->user?->name}} {{$guide?->user?->last_name}}
            </h2>
            <p style="margin-bottom:10px;">
            {{$guide->bio}}
            </p>
           
            
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
          <h6>{{ $guide?->user?->email ?? 'N/A' }}</h6>
     </div>
 </div>

<div class="stat">
   <img class="stat-icon" src="{{ asset('images/icons/icons8-phone-50.png') }}" alt="Plate Icon">
      <div>
         <h5>Contact</h5>
        <h6>{{ $guide?->user?->phone_number ?? 'N/A' }}</h6>
      </div>
 </div>

 <div class="stat">
   <img class="stat-icon" src="{{ asset('images/icons/icons8-around-the-globe-50.png') }}" alt="Plate Icon">
      <div>
         <h5>Country</h5>
             <h6>{{ $guide?->user?->country ?? 'N/A' }}</h6>
      </div>
 </div>

 <div class="stat">
   <img class="stat-icon" src="{{ asset('images/icons/icons8-country-50.png') }}" alt="Plate Icon">
      <div>
         <h5>Address</h5>
             <h6>{{ $guide?->address ?? 'N/A' }}</h6>
      </div>
 </div>



 <div class="stat">
   <img class="stat-icon" src="{{ asset('images/icons/icons8-licence-plate-50.png') }}" alt="Plate Icon">
      <div>
         <h5>Experience</h5>
             <h6>{{ $guide?->years_of_experience ?? 'N/A' }} Years</h6>
      </div>
 </div>

  <div class="stat">
   <img class="stat-icon" src="{{ asset('images/icons/icons8-licence-plate-50.png') }}" alt="Plate Icon">
      <div>
         <h5>Languages</h5>
             <h6>{{ $guide?->languages ?? 'N/A' }}</h6>
      </div>
 </div>

   

 <div class="stat">
   <img class="stat-icon" src="{{ asset('images/icons/icons8-category-50.png') }}" alt="Plate Icon">
      <div>
         <h5>Certificate image</h5>
             
              <button onclick="document.getElementById('license-modal-{{ $guide->id }}').classList.remove('hidden')"
                class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm font-medium">
                  View Certificate
              </button>
  
              <div id="license-modal-{{ $guide->id }}" 
               class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    
                  {{-- This is the main container for the modal content --}}
                  <div class="license-modal-container">
          
                     {{-- Close Button --}}
                         <button onclick="document.getElementById('license-modal-{{ $guide->id }}').classList.add('hidden')"
                           class="license-modal-close-btn">
                               &times;
                         </button>
            
                     {{-- License Image --}}
                        @if($guide->certificate_image)
                             <img src="{{ asset('storage/' . $guide->certificate_image) }}" 
                             alt="License Photo" 
                            class="license-modal-image">
                         @else
                        <p class="text-center text-gray-500">No certificate photo available</p>
                         @endif

                   </div>
              </div>
          </div>
      </div>
     </div>
   </div>
</div>

</x-app-layout>
