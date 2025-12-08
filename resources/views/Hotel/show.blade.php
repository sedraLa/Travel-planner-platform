@php use App\Enums\UserRole; @endphp
<x-app-layout>

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
    var lat = parseFloat(@json($coords['latitude']));
    var lon = parseFloat(@json($coords['longitude']));
    console.log("lat:", lat, "lon:", lon);

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

    
    <!--Hero background-->
     <div class="main-wrapper">
      <div class="hero-background" style="background-image: url('{{ $primaryImage ? asset('storage/' . $primaryImage->image_url) : '' }}');">
        <div class="headings" >
            <h1>{{$hotel->name}}</h1>
            <p style="letter-spacing: normal; font-size: 18px;">{{$hotel->description}} </p>
            <div class="rating-location">
                <div class="location">
                    <img src="/images/icons/location-dot-solid-full (4).svg" class="heading-icon">
                    <h5>{{$hotel->destination->city}},{{$hotel->destination->country}} </h5>
                </div>
                <a style="color:#f4f4f4;" href="">
                <div class="rating">
                    <span >GLOBAL RATING: {{$hotel->global_rating}} ⭐</span>
                </div>
            </a>


              </div>
        </div>
    </div>

</div>
    <!--Main page-->
    <div class="main-container">

          <!--Maps section-->
<div id="hotel-map" style="width: 100%; height: 400px; margin: 20px 0; border-radius: 15px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.1);"></div>

        <!--Highlights section-->
        <div class="highlights-container">
            <div class="highlights-header">
                <h1>Must-See Amenities</h1>
                <p>The absolute essentials that defines this Hotel</p>
            </div>


            @php
              $amenities = $hotel->amenities ?? [];
            @endphp
            <!--Highlight cards-->
            <div class="highlights-cards">
                  
    @forelse ($amenities as $amenity)
        <div class="highlight-card">
            <span>✓</span>
           <p>{{ $amenity }}</p>
        </div>
    @empty
        <p>No Amenities  available for this destination.</p>
    @endforelse
</div>
        </div>

        

        <!--Essential info-->
        <div class="highlights-header" style="text-align: center;">
            <h1>Essential Information</h1>
            <p>Everything you need to know before you book</p>
        </div>
        <div class="info-combined-card">
            <div class="info-sections">

              <!-- Getting Around -->
              <div class="info-section">
                <h3><img src="{{ asset('images/icons/star-icon.png') }}" alt=""> General Info</h3>
                <div class="info-grid two-col">
                  <div>
                    <span style="display: inline-flex; align-items: center; gap: 5px;"><img src="{{ asset('images/icons/address.png') }}" alt=""   style="width:20px; height:20px;">  ADDRESS</span>
                    <p>{{$hotel->address}}</p>
                  </div>
                  
                  <div>
                    <span>Nearby landmarks</span>
                    <p>{{$hotel->nearby_landmarks}}</p>
                  </div>

                </div>
              </div>

              <hr>

              <!-- Ratings & Rooms -->
              <div class="info-section">
                <h3><img src="/images/icons/hotel.png" alt="">Ratings & RoomsRatings</h3>
                <div class="info-grid two-col">
                  <div>
                    <span>STARS</span>
                        @php
                            $stars = $hotel->stars;
                        @endphp
                      <p>
                         @for ($i = 0; $i < $stars; $i++)
                             🌟
                          @endfor
                        </p>
                  </div>


                  <div>
                    <span style="display: inline-flex; align-items: center; gap: 5px;"> <img src="{{ asset('images/icons/rating.png') }}" alt=""  style="width:25px; height:25px;">GLOBAL RATING</span>
                    <p>{{$hotel->global_rating}}</p>
                  </div>

                  <div>
                    <span  style="display: inline-flex; align-items: center; gap: 5px;"> <img src="{{ asset('images/icons/room.png') }}" alt=""  style="width:25px; height:25px;">TOTAL ROOMS</span>
                    <p>{{$hotel->total_rooms}}</p>
                  </div>

                  <div>
                    <span style="display: inline-flex; align-items: center; gap: 5px;"> <img src="{{ asset('images/icons/dollar.png') }}" alt=""  style="width:25px; height:25px;">PRICE PER TIME</span>
                    <p>${{ number_format($hotel->price_per_night, 2) }}</p>
                  </div>

                </div>
              </div>

                  <hr>


              <!-- Policies & Pets -->

               <div class="info-section">
                <h3><img src="{{ asset('images/icons/alert.png') }}" alt="">
                    <img src="{{ asset('images/icons/alert1.png') }}" alt="">
                      Policies & Pets</h3>
                <div class="info-grid two-col">

                
                  <div>
                    <span  style="display: inline-flex; align-items: center; gap: 5px;"> <img src="{{ asset('images/icons/pets.png') }}" alt=""  style="width:25px; height:25px;">PETS</span>
                    <p>{{$hotel->pets_allowed }}</p>
                  </div>

                  

                  <div>
                    <span style="display: inline-flex; align-items: center; gap: 5px;"><img src="{{ asset('images/icons/in.png') }}" alt=""  style="width:25px; height:25px;">CHECK IN TIME</span>
                   <p>{{ $hotel->check_in_time ? \Carbon\Carbon::parse($hotel->check_in_time)->format('h:i A') : '' }}</p>
                   </div>

                  <div>
                    <span style="display: inline-flex; align-items: center; gap: 5px;"><img src="{{ asset('images/icons/out.png') }}" alt=""  style="width:25px; height:25px;">CHECK OUT TIME</span>
                    <p> {{ $hotel->check_out_time ? \Carbon\Carbon::parse($hotel->check_out_time)->format('h:i A') : '' }}</p>
                  </div>
                  

                  <div>
                    <span style="display: inline-flex; align-items: center; gap: 5px;"> <img src="{{ asset('images/icons/policies.png') }}" alt=""  style="width:25px; height:25px;">POLICIES</span>
                    <p>{{$hotel->policies}}</p>
                  </div>

                </div>
              </div>
                 <hr>


              <!-- Contact & Website -->

               <div class="info-section">
                <h3> <img src="{{ asset('images/icons/world.png') }}" alt=""> Contact & Website</h3>
                <div class="info-grid two-col">
                  <div>
                    <span style="display: inline-flex; align-items: center; gap: 5px;"><img src="{{ asset('images/icons/tel.png') }}" alt=""  style="width:25px; height:25px;">PHONE NUMBER</span>
                    <p>{{$hotel->phone_number}}</p>
                  </div>
                  <div>
                    <span style="display: inline-flex; align-items: center; gap: 5px;"> <img src="{{ asset('images/icons/email.png') }}" alt=""  style="width:25px; height:25px;">EMAIL</span>
                    <p>{{$hotel->email}}</p>
                  </div>
                  <div>
                    <span style="display: inline-flex; align-items: center; gap: 5px;"> <img src="{{ asset('images/icons/web.png') }}" alt=""  style="width:25px; height:25px;">WEBSITE</span>
                     <p>{!! $hotel->website !!}</p>
                  </div>
                </div>
              </div>
 
            </div>
          </div>


                  <!--Photo Gallery-->
        <div class="highlights-header" style="text-align: center; margin-top:65px;margin-bottom:-60px">
            <h1>Photo Gallery</h1>
            <p>Discover  {{$hotel->name}} hotel through stunning imagery </p>
        </div>
        <div class="photo-gallery">
            <div class="photo-gallery">
                @forelse ($hotel->images as $image)
                   <div class="photo">
                          <img src="{{ asset('storage/' . $image->image_url) }}" class="photo" alt="Hotel photo">
                    </div>
                @empty
                  <p>No photos available for this hotel.</p>
                @endforelse
              </div>

        </div>

    </div>


    <!-- Image Popup Overlay -->
<div id="image-popup" class="popup-overlay">
    <span class="close-btn">&times;</span>
    <img id="popup-image" src="" alt="Large View">
    <div class="arrow left">&#10094;</div>
    <div class="arrow right">&#10095;</div>
  </div>



</x-app-layout>
  <script>
    const images = document.querySelectorAll('.photo-gallery img');
    const popup = document.getElementById('image-popup');
    const popupImage = document.getElementById('popup-image');
    const closeBtn = document.querySelector('.close-btn');
    const leftArrow = document.querySelector('.arrow.left');
    const rightArrow = document.querySelector('.arrow.right');

    let currentIndex = 0;

    // Show popup when clicking image
    images.forEach((img, index) => {
      img.addEventListener('click', () => {
        popup.style.display = 'flex';
        popupImage.src = img.src;
        currentIndex = index;
      });
    });

    // Close popup
    closeBtn.addEventListener('click', () => {
      popup.style.display = 'none';
    });

    // Navigate left/right
    leftArrow.addEventListener('click', () => {
      currentIndex = (currentIndex - 1 + images.length) % images.length;
      popupImage.src = images[currentIndex].src;
    });

    rightArrow.addEventListener('click', () => {
      currentIndex = (currentIndex + 1) % images.length;
      popupImage.src = images[currentIndex].src;
    });

    // Close when clicking outside image
    popup.addEventListener('click', (e) => {
      if (e.target === popup) popup.style.display = 'none';
    });
  </script>




