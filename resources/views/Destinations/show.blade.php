@php use App\Enums\UserRole;@endphp
<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{asset('css/details.css')}}">
    @endpush

    {{--body content--}}

{{--
    <div class="main-wrapper">
    <div class="hero-background" style="background-image: url('{{ $primaryImage ? asset('storage/' . $primaryImage->image_url) : '' }}');">
        <div class="headings" style="font-size:32px;">
            <h1>{{$destination->name}}</h1>
            <h3>{{$destination->city}}</h3>
        </div>

        </div>
        </div>
        <div class="details">
            @if (session('success'))
            <div class="mb-4 px-4 py-3 bg-green-100 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif
            <header>Explore everything about this city</header>
            @if (Auth::user()->role === UserRole::ADMIN->value)
            <!-- Edit Destination Button -->
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Edit Destination') }}
                </h2>
                <a href="{{ route('destinations.edit', $destination->id) }}"

                   class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded shadow transition duration-200">
                    Edit
                </a>

                <form action="{{ route('destination.destroy', $destination->id) }}" method="POST"
                    onsubmit="return confirm('Are you sure you want to delete this destination?');">
                  @csrf
                  @method('DELETE')
                  <button type="submit"
                          class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded shadow transition duration-200">
                      Delete
                  </button>
              </form>
            </div>

            @endif
            <div class="cards">
                @foreach($destination->images as $image)
                    @if(!$image->is_primary)
                        <div class="card">
                            <img src="{{ asset('storage/' . $image->image_url) }}" alt="Destination Image">
                        </div>
                    @endif
                @endforeach
            </div>
            <div class="container">
                    <div class="location">
                <h1>Location</h1>
                <p>{{$destination->location_details}}</p>
            </div>
            <div class="flight-deals">
                <h1>Flight deals to {{$destination->name}}</h1>
            <p>{{$destination->description}}</p>
            </div>
            <div class="things">
                <h1>Things to do</h1>
                    <p>{{ $destination->activities }}</p>
            </div>

            <div class="weather">
                <h1>Weather info</h1>
                <a  href="{{route('weather.forecast',['city'=>$destination->name])}}">Click here to view 5-Day Forecast</a>
            </div>
            </div>
        </div>
    </div>

    --}}

     <!--Hero background-->
     <div class="main-wrapper">
        <div class="hero-background" style="background-image: url('{{ $primaryImage ? asset('storage/' . $primaryImage->image_url) : '' }}');">>
        <div class="headings" >
            <h1>{{$destination->name}}</h1>
            <p style="letter-spacing: normal; font-size: 18px;">{{$destination->description}} </p>
            <div class="rating-location">
                <div class="location">
                    <img src="/images/icons/location-dot-solid-full (4).svg" class="heading-icon">
                    <h5>{{$destination->city}},{{$destination->country}} </h5>
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
        <!--Highlights section-->
        <div class="highlights-container">
            <div class="highlights-header">
                <h1>Must-See Highlights</h1>
                <p>The absolute essentials that defines this destination</p>
            </div>

            <!--Highlight cards-->
            <div class="highlights-cards">
    @forelse ($destination->highlights as $highlight)
        <div class="highlight-card">
            <span>✓</span>
            <p>{{ $highlight->title }}</p>
        </div>
    @empty
        <p>No highlights available for this destination.</p>
    @endforelse
</div>
        </div>

        <!--Experiences section-->
        <div class="experiences-container">
            <div class="exp-header">
                <h1>Unforgettable Experiences</h1>
                <p>The absolute essentials that define this destination</p>
            </div>
            <!--Experiences cards-->
            <div class="exp-cards">
                <div class="exp-card">
                    <img src="{{asset('images/photo_٢٠٢٥-٠٤-٠٩_١٣-٠٦-٣١.jpg')}}"  alt="Activity 1">
                    <h3>Sunset Cruise</h3>
                    <p>Enjoy a beautiful sunset cruise along the cliffs of Santorini.</p>
                    <button>More Details</button>
                </div>
                <div class="exp-card">
                    <img src="{{asset('images/photo_٢٠٢٥-٠٤-٠٩_١٣-٠٦-٣٥.jpg')}}" alt="Activity 2">
                    <h3>Wine Tasting</h3>
                    <p>Sample the finest local wines in a traditional vineyard.</p>
                    <button>More Details</button>
                </div>
                <div class="exp-card">
                    <img src="{{asset('images/photo_٢٠٢٥-٠٤-٠٩_١٣-٠٦-٣٥.jpg')}}" alt="Activity 3">
                    <h3>Beach Day</h3>
                    <p>Relax on the pristine beaches and swim in the crystal-clear waters.</p>
                    <button>More Details</button>
                </div>
                <div class="exp-card">
                    <img src="{{asset('images/photo_٢٠٢٥-٠٤-٠٩_١٣-٠٦-٣٥.jpg')}}" alt="Activity 3">
                    <h3>Beach Day</h3>
                    <p>Relax on the pristine beaches and swim in the crystal-clear waters.</p>
                    <button>More Details</button>
                </div>
            </div>

        </div>


        <!--Essential info-->
        <div class="highlights-header" style="text-align: center;">
            <h1>Essential Information</h1>
            <p>Everything you need to know before you go</p>
        </div>
        <div class="info-combined-card">
            <div class="info-sections">
              <!-- Location Info -->
              <div class="info-section">
                <h3><img src="/images/icons/location-dot-solid-full (4).svg" alt=""> Location Details</h3>
                <div class="info-grid">
                  <div>
                    <span>TIMEZONE</span>
                    <p>{{$destination->timezone}}</p>
                  </div>
                  <div>
                    <span>LANGUAGE</span>
                    <p>{{$destination->language}}</p>
                  </div>
                  <div>
                    <span>CURRENCY</span>
                    <p>{{$destination->currency}}</p>
                  </div>
                  <div>
                    <span>WEATHER</span>
                    <a  href="{{route('weather.forecast',['city'=>$destination->name])}}">
                    <button class="weather-btn">10-Day Forecast</button>
                    </a>
                  </div>
                </div>
              </div>

              <hr>

              <!-- Getting Around -->
              <div class="info-section">
                <h3><img src="/images/icons/plane-departure-solid-full (1).svg" alt=""> Getting Around</h3>
                <div class="info-grid two-col">
                  <div>
                    <span>NEAREST AIRPORT</span>
                    <p>{{$destination->nearest_airport}}</p>
                  </div>
                  <div>
                    <span>PUBLIC TRANSPORT</span>
                     <p> Click <a href="{{ route('transport.index') }}" class="text-blue-600 underline hover:text-blue-800">here</a> 
                                         to view available transport options. </p>
                  </div>
                </div>
              </div>

              <hr>

              <!-- Travel Tips -->
              <div class="info-section">
                <h3><img src="/images/icons/suitcase-solid-full.svg" alt=""> Travel Tips & Essentials</h3>
                <div class="info-grid two-col">
                  <div>
                    <span>BEST TIME TO VISIT</span>
                    <p>{{$destination->best_time_to_visit}}</p>
                  </div>
                  <div>
                    <span>EMERGENCY NUMBERS</span>
                    <p>{{$destination->emergency_numbers}}</p>
                  </div>
                  <div>
                    <span>LOCAL TIP</span>
                    <p>{{$destination->local_tip}}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
                  <!--Photo Gallery-->
        <div class="highlights-header" style="text-align: center; margin-top:65px;margin-bottom:-60px">
            <h1>Photo Gallery</h1>
            <p>Discover the beauty of {{$destination->name}} through stunning imagery </p>
        </div>
        <div class="photo-gallery">
            <div class="photo-gallery">
                @forelse ($destination->images as $image)
                   <div class="photo">
                          <img src="{{ asset('storage/' . $image->image_url) }}" class="photo" alt="Destination photo">
                    </div>
                @empty
                  <p>No photos available for this destination.</p>
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

</x-app-layout>
