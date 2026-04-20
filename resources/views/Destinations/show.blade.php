@php use App\Enums\UserRole; @endphp
<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/transport.css') }}">
        <link rel="stylesheet" href="{{ asset('css/destinations.css') }}">
        <link rel="stylesheet" href="{{ asset('css/details.css') }}">
    @endpush

    <!--Hero background-->
    <div class="main-wrapper">
        <div class="hero-background" style="background-image: url('{{ $primaryImage ? asset('storage/' . $primaryImage->image_url) : '' }}');">
            <div class="headings">
                <h1>{{ $destination->name }}</h1>
                <p style="letter-spacing: normal; font-size: 18px;">{{ $destination->description }}</p>
                <div class="rating-location">
                    <div class="rating location">
                        <img src="/images/icons/location-dot-solid-full (4).svg" class="heading-icon">
                        <h5>{{ $destination->city }}, {{ $destination->country }}</h5>
                    </div>
                   <!-- <a style="color:#f4f4f4;" href="">
                        <div class="rating">
                            <span>⭐ 4.8 (12487 reviews)</span>
                        </div>
                    </a>-->
                </div>
            </div>
        </div>
    </div>

    <div class="main-container">

      <!-- AI SECTION -->
<div class="ai-pro-box">
    <div class="ai-pro-bg"></div>

    <div class="ai-pro-content">

        <div class="ai-pro-header">
            <div class="ai-pro-icon">🤖</div>
            <div>
                <div class="ai-pro-title">AI Travel Assistant</div>
                <div class="ai-pro-sub">Ask anything about {{ $destination->name }}</div>
            </div>
        </div>

        <div class="ai-pro-actions">
            <button class="ai-pro-btn" onclick="askPreset('Top things to do')">Top things</button>
            <button class="ai-pro-btn" onclick="askPreset('Best time to visit')">Best time</button>
            <button class="ai-pro-btn" onclick="askPreset('Travel tips')">Tips</button>
            <button class="ai-pro-btn" onclick="askPreset('Laws')">Laws</button>
            <button class="ai-pro-btn" onclick="askPreset('Hidden gems')">Hidden gems</button>
            <button class="ai-pro-btn" onclick="askPreset('Food recommendations')">Food</button>
            <button class="ai-pro-btn" onclick="askPreset('Safety tips')">Safety</button>
        </div>

        <div class="ai-pro-chat">
            <input id="ai-input" type="text" placeholder="Ask about this destination...">
            <button onclick="sendAI()">Ask</button>
        </div>

        <div id="ai-response" class="ai-pro-response"></div>

    </div>
</div>

    <!--Main page-->

        <!--Highlights section-->
        <div class="highlights-container animate">
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
                @forelse ($destination->activities as $activity)
                    <div class="exp-card">
                        <img 
                            src="{{ $activity->image ? asset('storage/' . $activity->image) : asset('images/default-activity.jpg') }}" 
                            alt="{{ $activity->name }}"
                        >
                        <h3>{{ $activity->name }}</h3>
                        <p>{{ $activity->description }}</p>
                        <a href="{{ route('Activity.show', $activity->id) }}" class="details-btn">
                            More Details
                                    </a>
                    </div>
                @empty
                    <p style="padding:20px;">No activities available for this destination.</p>
                @endforelse
            </div>
        </div>
        <a href="{{ route('destination.activities', $destination->id) }}" class="see-all-card">
            <div>
                <span>→</span>
                <p>View All Activities</p>
            </div>
        </a>
        </div>
{{--Trips section--}}
<div class="experiences-container">
    <div class="exp-header">
        <h1>Available Trips</h1>
        <p>Handpicked trips for this destination</p>
    </div>

    <div class="exp-cards">
        @forelse ($limitedTrips as $trip)
            <div class="exp-card">
                <img src="{{ $trip->images->first() ? asset('storage/' . $trip->images->first()->image_path) : asset('images/default-trip.jpg') }}">
                <h3>{{ $trip->name }}</h3>
                <p>{{ $trip->description }}</p>
                <p><strong>{{ $trip->duration_days }} days</strong></p>
            </div>
        @empty
            <p>No trips available.</p>
        @endforelse


    </div>
    <a href="{{ route('destination.trips', $destination->id) }}" class="see-all-card">
        <div>
            <span>→</span>
            <p>View All Trips</p>
        </div>
    </a>
</div>

        <!--Essential info-->
        <div class="highlights-header" style="text-align: center;">
            <h1>Essential Information</h1>
            <p>Everything you need to know before you go</p>
        </div>

        <div class="info-combined-card animate">
            <div class="info-sections">
                <!-- Location Info -->
                <div class="info-section">
                    <h3><img src="/images/icons/location-dot-solid-full (4).svg" alt=""> Location Details</h3>
                    <div class="info-grid">
                        <div><span>TIMEZONE</span><p>{{ $destination->timezone }}</p></div>
                        <div><span>LANGUAGE</span><p>{{ $destination->language }}</p></div>
                        <div><span>CURRENCY</span><p>{{ $destination->currency }}</p></div>
                        <div><span>WEATHER</span>
                            <a href="{{ route('weather.forecast',['city'=>$destination->name]) }}">
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
                        <div><span>NEAREST AIRPORT</span><p>{{ $destination->nearest_airport }}</p></div>
                        <div><span>PUBLIC TRANSPORT</span>
                         {{--   <p>Click <a href="{{ route('transport.index') }}" class="text-blue-600 underline hover:text-blue-800">here</a> to view available transport options.</p>--}}
                        </div>
                    </div>
                </div>
                <hr>

                <!-- Travel Tips -->
                <div class="info-section">
                    <h3><img src="/images/icons/suitcase-solid-full.svg" alt=""> Travel Tips & Essentials</h3>
                    <div class="info-grid two-col">
                        <div><span>BEST TIME TO VISIT</span><p>{{ $destination->best_time_to_visit }}</p></div>
                        <div><span>EMERGENCY NUMBERS</span><p>{{ $destination->emergency_numbers }}</p></div>
                        <div><span>LOCAL TIP</span><p>{{ $destination->local_tip }}</p></div>
                    </div>
                </div>
            </div>
        </div>

        <!--Photo Gallery-->
        <div class="highlights-header" style="text-align: center; margin-top:65px;margin-bottom:-60px">
            <h1>Photo Gallery</h1>
            <p>Discover the beauty of {{ $destination->name }} through stunning imagery </p>
        </div>
        <div class="photo-gallery">
            @forelse ($destination->images as $image)
               <div class="photo animate">
                      <img src="{{ asset('storage/' . $image->image_url) }}" class="photo" alt="Destination photo">
                </div>
            @empty
              <p>No photos available for this destination.</p>
            @endforelse
        </div>
    </div>

    <!-- Image Popup Overlay -->
    <div id="image-popup" class="popup-overlay">
        <span class="close-btn">&times;</span>
        <img id="popup-image" src="" alt="Large View">
        <div class="arrow left">&#10094;</div>
        <div class="arrow right">&#10095;</div>
    </div>

    <!-- Activity Modal -->
    <div id="activityModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <img id="modalImage" src="" alt="" class="modal-img">
            <h2 id="modalTitle"></h2>
            <p id="modalDescription"></p>
            <div class="modal-info">
                <p><strong>Destination:</strong> <span id="modalDestination"></span></p>
                <p><strong>Duration:</strong> <span id="modalDuration"></span></p>
                <p><strong>Price:</strong> $<span id="modalPrice"></span></p>
                <p><strong>Category:</strong> <span id="modalCategory"></span></p>
                <p><strong>Difficulty Level:</strong> <span id="modalDifficultyLevel"></span></p>
                <p><strong>Guide Name:</strong> <span id="modalGuideName"></span></p>
                <p><strong>Guide Language:</strong> <span id="modalGuideLanguage"></span></p>
                <p><strong>Availability:</strong> <span id="modalAvailability"></span></p>
                <p><strong>Requirements:</strong> <span id="modalRequirements"></span></p>
                <p><strong>Amenities:</strong> <span id="modalAmenities"></span></p>
                <p><strong>Highlights:</strong> <span id="modalHighlights"></span></p>
                <p><strong>Family Friendly:</strong> <span id="modalFamily"></span></p>
                <p><strong>Pets Allowed:</strong> <span id="modalPets"></span></p>
                <p><strong>Requires Booking:</strong> <span id="modalBooking"></span></p>
            </div>
        </div>
    </div>

    <script>
        // Image popup script
        const images = document.querySelectorAll('.photo-gallery img');
        const popup = document.getElementById('image-popup');
        const popupImage = document.getElementById('popup-image');
        const closeBtn = document.querySelector('.close-btn');
        const leftArrow = document.querySelector('.arrow.left');
        const rightArrow = document.querySelector('.arrow.right');
        let currentIndex = 0;

        images.forEach((img, index) => {
          img.addEventListener('click', () => {
            popup.style.display = 'flex';
            popupImage.src = img.src;
            currentIndex = index;
          });
        });
        closeBtn.addEventListener('click', () => { popup.style.display = 'none'; });
        leftArrow.addEventListener('click', () => {
          currentIndex = (currentIndex - 1 + images.length) % images.length;
          popupImage.src = images[currentIndex].src;
        });
        rightArrow.addEventListener('click', () => {
          currentIndex = (currentIndex + 1) % images.length;
          popupImage.src = images[currentIndex].src;
        });
        popup.addEventListener('click', (e) => { if(e.target === popup) popup.style.display = 'none'; });

        // Activity modal script
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('activityModal');
            const closeBtn = modal.querySelector('.close');
            const title = document.getElementById('modalTitle');
            const description = document.getElementById('modalDescription');
            const destination = document.getElementById('modalDestination');
            const duration = document.getElementById('modalDuration');
            const price = document.getElementById('modalPrice');
            const category = document.getElementById('modalCategory');
            const image = document.getElementById('modalImage');

            const guideName = document.getElementById('modalGuideName');
            const guideLang = document.getElementById('modalGuideLanguage');
            const availability = document.getElementById('modalAvailability');
            const requirements = document.getElementById('modalRequirements');
            const amenities = document.getElementById('modalAmenities');
            const highlights = document.getElementById('modalHighlights');
            const family = document.getElementById('modalFamily');
            const pets = document.getElementById('modalPets');
            const booking = document.getElementById('modalBooking');
            const difficulty = document.getElementById('modalDifficultyLevel');

            document.querySelectorAll('.details-btn').forEach(button => {
                button.addEventListener('click', () => {
                    title.textContent = button.dataset.name;
                    description.textContent = button.dataset.description;
                    destination.textContent = button.dataset.destination;
                    duration.textContent = button.dataset.duration;
                    price.textContent = button.dataset.price;
                    category.textContent = button.dataset.category;
                    image.src = button.dataset.image;

                    guideName.textContent = button.dataset.guide_name;
                    guideLang.textContent = button.dataset.guide_language;
                    availability.textContent = button.dataset.availability;
                    difficulty.textContent = button.dataset.difficulty_level;
                    requirements.textContent = button.dataset.requirements;
                    amenities.textContent = button.dataset.amenities;
                    highlights.textContent = button.dataset.highlights;
                    family.textContent = button.dataset.family_friendly;
                    pets.textContent = button.dataset.pets_allowed;
                    booking.textContent = button.dataset.requires_booking;

                    modal.style.display = 'block';
                });
            });

            closeBtn.addEventListener('click', () => modal.style.display = 'none');
            window.addEventListener('click', (e) => {
                if (e.target === modal) modal.style.display = 'none';
            });
        });
    </script>

    {{--Ai script--}}
    <script>
        let aiInput = document.getElementById("ai-input");
let aiResponse = document.getElementById("ai-response");

// preset click
function askPreset(question) {
    aiInput.value = question;
    sendAI();
}

// send question
function sendAI() {
    let question = aiInput.value.trim();
    if (!question) return;

    aiResponse.style.display = "block";
    aiResponse.innerHTML = "Thinking... 🤖";

    fetch("{{ route('ai.destination.ask') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({
            entity_id: {{ $destination->id }},
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
        aiResponse.innerHTML = data.answer;
    })
    .catch((error) => {
        aiResponse.innerHTML = error.message || "Something went wrong. Please try again.";
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

<style>
    /* Modal styling */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        padding-top: 80px;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(4px);
    }

    .modal-content {
        background: linear-gradient(135deg, #ffffff 0%, #f7f9fc 100%);
        margin: auto;
        padding: 35px;
        border-radius: 25px;
        width: 700px;
        max-width: 90%;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
        position: relative;
        text-align: left;
        animation: slideDown 0.4s ease;
        font-family: 'Poppins', sans-serif;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .modal-img {
        width: 100%;
        border-radius: 20px;
        margin-bottom: 20px;
        height: 250px;
        object-fit: cover;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
    }

    .modal h2 {
        color: var(--indigo);
        font-size: 28px;
        margin-bottom: 15px;
        text-align: center;
    }

    .modal p {
        color: #555;
        font-size: 15.5px;
        line-height: 1.6;
    }

    .modal-info {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px 20px;
        margin-top: 25px;
        padding: 20px;
        background: #f9fbff;
        border-radius: 15px;
        border: 1px solid #e5eaf2;
    }

    .modal-info p {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 15px;
        color: #333;
        margin: 0;
    }

    .modal-info strong {
        color: var(--indigo);
        font-weight: 600;
    }

    .close {
        position: absolute;
        top: 18px;
        right: 25px;
        font-size: 28px;
        font-weight: bold;
        color: #666;
        cursor: pointer;
        transition: color 0.2s;
    }

    .close:hover {
        color: var(--indigo);
    }


</x-app-layout>

