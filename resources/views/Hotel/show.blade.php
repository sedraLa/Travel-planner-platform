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


          <!-- Available Room Types -->
          <div class="highlights-header" style="text-align: center; margin-top:65px;">
            <h1>Available Room Types</h1>
            <p>Choose the room type that fits your stay and book directly</p>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mt-8 mb-10">
            @forelse($hotel->roomTypes as $roomType)
                @php
                    $primaryRoomImage = $roomType->images->firstWhere('is_primary', true) ?? $roomType->images->first();
                @endphp
                <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-gray-100">
                    <div class="h-48 bg-gray-100">
                        @if($primaryRoomImage)
                            <img src="{{ asset('storage/' . $primaryRoomImage->image_url) }}" alt="{{ $roomType->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-500 text-sm">No primary image</div>
                        @endif
                    </div>

                    <div class="p-5">
                        <h3 class="text-xl font-semibold text-gray-900">{{ $roomType->name }}</h3>
                        <p class="text-blue-700 font-semibold mt-1">${{ number_format($roomType->price_per_night, 2) }} / night</p>
                        <p class="text-sm text-gray-600 mt-1">Capacity: {{ $roomType->capacity }} guests</p>
                        <p class="text-sm text-gray-600 mt-1">Available now: {{ max(0, $roomType->quantity) }}</p>
                        <p class="text-sm text-gray-700 mt-3">{{ $roomType->description }}</p>

                        @if(!empty($roomType->amenities))
                            <div class="mt-3 flex flex-wrap gap-2">
                                @foreach($roomType->amenities as $amenity)
                                    <span class="px-2 py-1 rounded-full bg-gray-100 text-xs text-gray-700">{{ $amenity }}</span>
                                @endforeach
                            </div>
                        @endif

                        <p class="text-xs text-gray-500 mt-3">
                            {{ $roomType->is_refundable ? 'Refundable booking' : 'Non-refundable booking' }}
                        </p>

                        <button 
                        class="view-room-gallery-btn mt-4 text-sm font-medium text-blue-700"
                        data-images='@json($roomType->images->pluck("image_url"))'>
                        View Gallery
                    </button>

                        <details class="mt-4">
                            <summary class="cursor-pointer inline-flex items-center px-3 py-2 rounded-lg bg-blue-600 text-white text-sm hover:bg-blue-700">Select Room</summary>
                            <div class="mt-4 border border-gray-100 rounded-xl p-4">
                                <form method="GET" action="{{ route('reservations.form', $hotel->id) }}" class="space-y-3">
                                    <input type="hidden" name="room_type_id" value="{{ $roomType->id }}">

                                    <div>
                                        <label class="block text-xs text-gray-600 mb-1">Check In</label>
                                        <input type="date" name="check_in_date" class="w-full border-gray-300 rounded-lg text-sm" required>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-600 mb-1">Check Out</label>
                                        <input type="date" name="check_out_date" class="w-full border-gray-300 rounded-lg text-sm" required>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-600 mb-1">Guests</label>
                                        <input type="number" min="1" name="guest_count" class="w-full border-gray-300 rounded-lg text-sm" required>
                                    </div>

                                    <button type="submit" class="w-full bg-green-600 text-white py-2 rounded-lg text-sm hover:bg-green-700">
                                        Continue
                                    </button>
                                </form>
                            </div>
                        </details>
                    </div>
                </div>
            @empty
                <p class="text-gray-600 md:col-span-2 xl:col-span-3 text-center">No room types available at this hotel yet.</p>
            @endforelse
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

  <!-- ROOM IMAGE POPUP -->
<div id="room-popup" class="popup-overlay">
    <span class="room-close-btn">&times;</span>
    <img id="room-popup-image" src="" alt="Room View">
    <div class="room-arrow left">&#10094;</div>
    <div class="room-arrow right">&#10095;</div>
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
        fetch("{{ route('ai.hotel.ask') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                entity_id: {{ $hotel->id }},
                question
            })
        })
        .then(async (res) => {
            const data = await res.json();
            if (!res.ok) {
                throw new Error(data.answer || "Could not get AI response.");
            }
            return data;
        })
        .then((data) => {
            hotelResponse.innerHTML = data.answer;
        })
        .catch((error) => {
            hotelResponse.innerHTML = error.message || "Something went wrong. Please try again.";
        });
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
<script>
    document.addEventListener("DOMContentLoaded", function () {
    
        const buttons = document.querySelectorAll('.view-room-gallery-btn');
    
        const popup = document.getElementById('room-popup');
        const popupImage = document.getElementById('room-popup-image');
        const closeBtn = document.querySelector('.room-close-btn');
        const leftArrow = document.querySelector('.room-arrow.left');
        const rightArrow = document.querySelector('.room-arrow.right');
    
        let images = [];
        let currentIndex = 0;
    
        // فتح الجاليري
        buttons.forEach(btn => {
            btn.addEventListener('click', () => {
                const imgs = JSON.parse(btn.dataset.images);
    
                if (!imgs.length) return;
    
                images = imgs.map(img => `/storage/${img}`);
                currentIndex = 0;
    
                popup.style.display = 'flex';
                popupImage.src = images[currentIndex];
            });
        });
    
        // إغلاق
        closeBtn.addEventListener('click', () => {
            popup.style.display = 'none';
        });
    

        leftArrow.addEventListener('click', () => {
            currentIndex = (currentIndex - 1 + images.length) % images.length;
            popupImage.src = images[currentIndex];
        });
    
  
        rightArrow.addEventListener('click', () => {
            currentIndex = (currentIndex + 1) % images.length;
            popupImage.src = images[currentIndex];
        });
    
     
        popup.addEventListener('click', (e) => {
            if (e.target === popup) {
                popup.style.display = 'none';
            }
        });
    
    });
    </script>
