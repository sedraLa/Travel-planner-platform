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
    
    <!--Hero background-->
     <div class="main-wrapper">
        <div class="hero-background" style="background-image: url('{{ $primaryImage ? asset('storage/' . $primaryImage->image_url) : '' }}');">>
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
                    <span >⭐ 4.8 (12487 reviews)</span>
                </div>
            </a>


              </div>
        </div>
    </div>

</div>
    <!--Main page-->
    <div class="main-container">

          <!--Maps section-->
@push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                var lat = @json($coords['latitude'] ?? null);
                var lon = @json($coords['longitude'] ?? null);
                console.log("Latitude:", lat, "Longitude:", lon);

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
                <h3><img src="/images/icons/building-solid.svg" alt=""> General Info</h3>
                <div class="info-grid two-col">
                  <div>
                    <span>ADDRESS</span>
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
                <h3><img src="/images/icons/star-solid.svg" alt="">Ratings & RoomsRatings & Rooms</h3>
                <div class="info-grid two-col">
                  <div>
                    <span>STARS</span>
                    <p>{{$hotel->stars}}</p>
                  </div>

                  <div>
                    <span>GLOBAL RATING</span>
                    <p>{{$hotel->global_rating}}</p>
                  </div>

                  <div>
                    <span>TOTAL ROOMS</span>
                    <p>{{$hotel->total_rooms}}</p>
                  </div>

                  <div>
                    <span>PRICE PER TIME</span>
                    <p>{{$hotel->price_per_night}}</p>
                  </div>

                </div>
              </div>

                  <hr>


              <!-- Policies & Pets -->

               <div class="info-section">
                <h3><img src="/images/icons/paw-solid.svg" alt=""> Policies & Pets</h3>
                <div class="info-grid two-col">
                  <div>
                    <span>PET (ALLOWED OR NOT ALLOWED)</span>
                    <p> {{ $hotel->pets_allowed }}</p>
                  </div>
                  <div>
                    <span>CHECK IN TIME</span>
                    <p>{{$hotel->check_in_time}}</p>
                  </div>
                  <div>
                    <span>CHECK OUT TIME</span>
                    <p>{{$hotel->check_out_time}}</p>
                  </div>
                  

                  <div>
                    <span>POLICIES</span>
                    <p>{{$hotel->policies}}</p>
                  </div>

                </div>
              </div>
                 <hr>


              <!-- Contact & Website -->

               <div class="info-section">
                <h3> <img src="/images/icons/phone-solid.svg" alt=""> Contact & Website</h3>
                <div class="info-grid two-col">
                  <div>
                    <span>PHONE NUMBER</span>
                    <p>{{$hotel->phone_number}}</p>
                  </div>
                  <div>
                    <span>EMAIL</span>
                    <p>{{$hotel->email}}</p>
                  </div>
                  <div>
                    <span>WEBSITE</span>
                    <p>{{$hotel->website}}</p>
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




