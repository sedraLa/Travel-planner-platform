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

    if (!isNaN(lat) && !isNaN(lon)) {
        var map = L.map('hotel-map').setView([lat, lon], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        L.marker([lat, lon]).addTo(map)
            .bindPopup("{{ addslashes($hotel->name) }}")
            .openPopup();
    } else {
        document.getElementById('hotel-map').innerHTML =
            "<p style='padding:20px;text-align:center;'>Map data not available</p>";
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
                    <a class="reviews-btn" href="{{ route('hotels.reviews.index', $hotel->id) }}">
                        <span>Reviews</span>
                        <span class="reviews-count">{{ $hotel->reviews->count() }}</span>
                    </a>
                    <img src="/images/icons/location-dot-solid-full (4).svg" class="heading-icon">
                    <h5>{{$hotel->destination->city}},{{$hotel->destination->country}} </h5>
                </div>
                <a style="color:#f4f4f4;" href="">
                    <div class="rating" style="display:flex;align-items:center;gap:6px;">
                        <span style="color:#f59e0b;font-weight:600;">
                            ⭐ {{ number_format($hotel->average_rating, 1) }}
                        </span>

                        <span style="font-size:13px;color:#ddd;">
                            ({{ $hotel->reviews_count }} reviews)
                        </span>
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

<!-- AI SECTION FOR HOTEL -->
<div class="ai-pro-box">
    <div class="ai-pro-bg"></div>

    <div class="ai-pro-content">

        <div class="ai-pro-header">
            <div class="ai-pro-icon">🏨</div>
            <div>
                <div class="ai-pro-title">AI Hotel Assistant</div>
                <div class="ai-pro-sub">Ask anything about {{ $hotel->name }}</div>
            </div>
        </div>

        <div class="ai-pro-actions">
            <button class="ai-pro-btn" onclick="askHotelPreset('Is this hotel worth the price?')">Worth it?</button>
            <button class="ai-pro-btn" onclick="askHotelPreset('Is it good for couples or honeymoon?')">For Couples</button>
            <button class="ai-pro-btn" onclick="askHotelPreset('What are the pros and cons of this hotel?')">Pros & Cons</button>
            <button class="ai-pro-btn" onclick="askHotelPreset('Which room should I choose?')">Best Rooms</button>
            <button class="ai-pro-btn" onclick="askHotelPreset('Any hidden gems nearby?')">Nearby Spots</button>
            <button class="ai-pro-btn" onclick="askHotelPreset('Tips to get best price')">Save Money</button>
            <button class="ai-pro-btn" onclick="askHotelPreset('Is it good for business travel?')">Business</button>
        </div>

        <div class="ai-pro-chat">
            <input id="hotel-ai-input" type="text" placeholder="Ask about this hotel...">
            <button onclick="sendHotelAI()">Ask</button>
        </div>

        <div id="hotel-ai-response" class="ai-pro-response"></div>

    </div>
</div>
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


<script>
    let hotelInput = document.getElementById("hotel-ai-input");
    let hotelResponse = document.getElementById("hotel-ai-response");

    // preset
    function askHotelPreset(q) {
        hotelInput.value = q;
        sendHotelAI();
    }

    // send
    function sendHotelAI() {
        let question = hotelInput.value.trim();
        if (!question) return;

        hotelResponse.style.display = "block";
        hotelResponse.innerHTML = "Thinking... 🤖";

        setTimeout(() => {
            hotelResponse.innerHTML = generateHotelResponse(question);
        }, 700);
    }

    // fake brain
    function generateHotelResponse(q) {
    q = q.toLowerCase();

    const price = {{ $hotel->price_per_night }};
    const city = "{{ $hotel->destination->city }}";
    const country = "{{ $hotel->destination->country }}";
    const amenities = @json($hotel->amenities ?? []);
    const nearby = "{{ $hotel->nearby_landmarks }}";
    const pets = "{{ $hotel->pets_allowed }}";

    // 💰 value / worth
    if (q.includes("worth") || q.includes("price")) {
        return `At $${price} per night in ${city}, this hotel is ${price > 150 ? "mid-to-high range" : "budget-friendly"}. It offers decent value depending on season and demand.`;
    }

    // 💑 couples / honeymoon
    if (q.includes("couple") || q.includes("honeymoon")) {
        return "This hotel is suitable for couples looking for comfort and convenience. If you want privacy and quiet stay, higher floors are recommended.";
    }

    // ⚖️ pros & cons
    if (q.includes("pros") || q.includes("cons")) {
        return `Pros: good location in ${city}, ${amenities.slice(0,2).join(", ")}. Cons: depends on availability and seasonal pricing.`;
    }

    // 🛏️ rooms
    if (q.includes("room")) {
        return "Higher floors usually offer better views and less noise. Rooms facing city side are more recommended.";
    }

    // 💸 saving money
    if (q.includes("save") || q.includes("best price")) {
        return "Book early or during off-season. Weekdays are usually cheaper than weekends.";
    }

    // 📍 nearby
    if (q.includes("nearby")) {
        return `Around the hotel you can find: ${nearby}. Some hidden local spots are usually within walking distance.`;
    }

    // 💼 business
    if (q.includes("business")) {
        return "Good for business travel due to location and accessibility, but check Wi-Fi speed and workspace availability.";
    }

    // 🐾 pets
    if (q.includes("pets")) {
        return `Pets allowed: ${pets}. Always confirm restrictions for size or breed.`;
    }

    // default
    return "I can help you decide if this hotel is worth it, good for couples, business travel, or how to get the best deal.";
}
    </script>

{{--animation--}}
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const elements = document.querySelectorAll(".highlights-container, .exp-card, .info-combined-card, .photo, .ai-pro-box");

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add("show");
                }
            });
        }, {
            threshold: 0.15
        });

        elements.forEach(el => {
            el.classList.add("animate");
            observer.observe(el);
        });
    });
    </script>