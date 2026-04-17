<x-app-layout>
 @push('styles')
    <link rel="stylesheet" href="{{asset('css/trip.css')}}">
    @endpush

<div class="page-wrap">

    {{-- ══ HERO ══════════════════════════════════════════ --}}
    <div class="hero">
        <div class="hero-top">
            @if($trip->category)
                <span class="badge">{{ ucfirst($trip->category) }}</span>
            @endif
           @if($trip->schedules->first())
            <span class="status-pill">
              {{ ucfirst($trip->schedules->first()->status) }}
             </span>
            @endif

            <div class="reviews-chip" onclick="document.getElementById('reviews-section').scrollIntoView({behavior:'smooth'})">
                ⭐ {{ number_format($averageRating ?? 0, 1) }} ({{ $reviewsCount }}) Reviews
            </div>

        </div>
        <h1>{{ $trip->name }}</h1>
        @if($trip->description)
            <p>{{ $trip->description }}</p>
        @endif
        <div class="meta-row">
            <span class="meta-chip">📅 {{ $trip->duration_days }} days</span>
            <span class="meta-chip">👥 Max {{ $trip->max_participants }} participants</span>
            @if($trip->primaryDestination)
                <span class="meta-chip">📍 {{ $trip->primaryDestination->name }}</span>
            @endif
            @if($trip->category)
                <span class="meta-chip">🏷 {{ $trip->category }}</span>
            @endif
        </div>
    </div>

    {{-- ══ STAT CARDS ═══════════════════════════════════ --}}
    <div class="grid3">
        <div class="stat-card">
            <div class="stat-label">Duration</div>
            <div class="stat-value">{{ $trip->duration_days }}</div>
            <div class="stat-sub">Days total</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Max participants</div>
            <div class="stat-value">{{ $trip->max_participants }}</div>
            <div class="stat-sub">Per departure</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Starting from</div>
            @php $lowestPkg = $trip->packages->sortBy('price')->first(); @endphp
            <div class="stat-value" style="font-size:18px;color:#185FA5">
                {{ $lowestPkg ? '$'.number_format($lowestPkg->price) : '—' }}
            </div>
            <div class="stat-sub">Per person</div>
        </div>
    </div>

    {{-- ══ DAILY ITINERARY ══════════════════════════════ --}}
    @if($trip->days->count())
    <div class="section">
        <div class="section-title">Daily itinerary</div>
        @foreach($trip->days->sortBy('day_number') as $day)
        <div class="day-card">
            <div class="day-header" onclick="toggleDay(this)">
                <div class="day-num">{{ $day->day_number }}</div>
                <div>
                    <div class="day-name">{{ $day->title }}</div>
                    <div class="day-sub">
                        {{ $trip->primaryDestination?->name ?? '' }}
                        @if($day->hotel) &nbsp;•&nbsp; {{ $day->hotel->name }} @endif
                    </div>
                </div>
                <div class="day-chevron {{ $loop->first ? 'open' : '' }}">⌄</div>
            </div>
            <div class="day-body {{ $loop->first ? 'open' : '' }}">
                @if($day->description)
                    <p class="day-desc">{{ $day->description }}</p>
                @endif

                {{-- Highlights --}}
                @if($day->highlights)
                    <div class="highlights-row">
                        @foreach($day->highlights as $hl)
                            <span class="highlight-tag">{{ $hl }}</span>
                        @endforeach
                    </div>
                @endif

                {{-- Hotel --}}
                @if($day->hotel)
                    <div class="hotel-box">
                        <div class="hotel-icon">🏨</div>
                        <div>
                            <div style="font-size:13px;font-weight:500;color:#1a1a1a">{{ $day->hotel->name }}</div>
                            @if(isset($day->hotel->stars))
                                <div style="font-size:12px;color:#888">{{ $day->hotel->stars }}-star</div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Activities --}}
                @if($day->activities->count())
                    <div class="activities-list">
                        @foreach($day->activities->sortBy('start_time') as $da)
                        <div class="activity-item">
                            <div class="activity-time">
                                {{ $da->start_time ? \Carbon\Carbon::parse($da->start_time)->format('H:i') : '' }}
                                @if($da->end_time) – {{ \Carbon\Carbon::parse($da->end_time)->format('H:i') }} @endif
                            </div>
                            <div>
                                <div class="activity-name">{{ $da->activity?->name ?? 'Activity' }}</div>
                                @if($da->notes)
                                    <div class="activity-note">{{ $da->notes }}</div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- ══ MEETING POINT ════════════════════════════════ --}}
@if($trip->meeting_point_address || $trip->meeting_point_description)
<div class="section">
    <div class="section-title">Meeting point</div>

    <div class="map-box">

        @if($coords)
            <div id="trip-map-wrapper">
                <div id="trip-map"></div>
            </div>
        @else
            <div class="map-no-location">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none"
                     stroke="#B4B2A9" stroke-width="1.5">
                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/>
                    <circle cx="12" cy="9" r="2.5"/>
                </svg>
                <span>Location not available for this meeting point</span>
            </div>
        @endif

        <div class="map-info">
            @if($trip->meeting_point_description)
            <div>
                <div class="map-field-label">Description</div>
                <div class="map-field-val">{{ $trip->meeting_point_description }}</div>
            </div>
            @endif

            @if($trip->meeting_point_address)
            <div>
                <div class="map-field-label">Address</div>
                <div class="map-field-val">{{ $trip->meeting_point_address }}</div>
            </div>
            @endif
        </div>

    </div>
</div>
@endif


    {{-- ══ PACKAGES ═════════════════════════════════════ --}}
    @if($trip->packages->count())
    <div class="section">
        <div class="section-title">Packages</div>
        @foreach($trip->packages as $pkg)
        <div class="package-card">
            <div class="package-head">
                <div>
                    <div style="display:flex;align-items:center;gap:6px">
                        <span class="pkg-name">{{ $pkg->name }}</span>
                        @if($loop->first)
                            <span class="popular-badge">Most popular</span>
                        @endif
                    </div>
                    @if($pkg->infos->count())
                        <div style="font-size:13px;color:#888;margin-top:3px">{{ $pkg->infos->first()->content }}</div>
                    @endif
                </div>
                <div class="pkg-price">${{ number_format($pkg->price) }} <span>/ person</span></div>
            </div>

            {{-- Highlights --}}
            @if($pkg->highlights->count())
                <div class="highlights-row">
                    @foreach($pkg->highlights as $hl)
                        <span class="highlight-tag">{{ $hl->title }}</span>
                    @endforeach
                </div>
            @endif

            <div class="inc-exc">
                @if($pkg->includes->count())
                <div class="list-col">
                    <div class="col-label-green">Included</div>
                    <ul>
                        @foreach($pkg->includes as $inc)
                            <li><span class="dot-g"></span>{{ $inc->content }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                @if($pkg->excludes->count())
                <div class="list-col">
                    <div class="col-label-red">Excluded</div>
                    <ul>
                        @foreach($pkg->excludes as $exc)
                            <li><span class="dot-r"></span>{{ $exc->content }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                {{--change design later--}}
                <a href="{{ route('trip.booking.form', $pkg->id) }}" class="btn-book" style="width:40%">
                    <span style = "color:white;">Book Now</span>
                    <svg class="btn-icon" viewBox="0 0 24 24" fill="none">
                        <path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </a>
            </div>

            {{-- Hotels in package --}}
            @if($pkg->packageHotels->count())
                <div style="margin-top:1rem">
                    <div style="font-size:12px;color:#888;margin-bottom:6px">Accommodation</div>
                    @foreach($pkg->packageHotels as $ph)
                        <div class="hotel-box" style="margin-bottom:6px">
                            <div class="hotel-icon">🏨</div>
                            <div>
                                <div style="font-size:13px;font-weight:500;color:#1a1a1a">{{ $ph->hotel?->name ?? 'Hotel' }}</div>
                                <div style="font-size:12px;color:#888">
                                    {{ $ph->room_type }}
                                    @if($ph->meal_plan) &nbsp;•&nbsp; {{ $ph->meal_plan }} @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Additional info blocks --}}
            @foreach($pkg->infos->skip(1) as $info)
                <div class="info-block">{{ $info->content }}</div>
            @endforeach
        </div>
        @endforeach
    </div>
    @endif

    @if($reviews->count())
    <div class="section" id="reviews-section">
    
        <div class="section-title">
            Reviews ({{ $reviewsCount }})
        </div>
    
        <div style="display:grid;gap:12px;">
    
            @foreach($reviews as $review)
            <div class="review-card">
    
                <div class="review-top">
                    <div class="user-name">
                        {{ $review->user?->full_name ?? 'Anonymous' }}
                    </div>
    
                    <div class="date">
                        {{ $review->created_at->format('d M Y') }}
                    </div>
                </div>
    
                <div class="rating">
                    @for($i=0; $i < 5; $i++)
                        <span style="color:{{ $i < $review->rating ? '#f59e0b' : '#e5e7eb' }}">★</span>
                    @endfor
                </div>
    
                <div class="review-text">
                    {{ $review->review }}
                </div>
    
            </div>
            @endforeach
    
        </div>
    
      
        <div style="margin-top:15px;text-align:center;">
            <a href="{{ route('trip.reviews.index', $trip->id) }}"
               style="display:inline-block;padding:10px 16px;background:#185FA5;color:#fff;border-radius:10px;text-decoration:none;">
                View all reviews
            </a>
        </div>
    
    </div>
    @endif

    {{-- ══ SCHEDULES + GUIDES + TRANSPORT ══════════════ --}}
    <div class="grid2">
        {{-- Schedules --}}
        @if($trip->schedules->count())
        <div class="side-card">
            <div class="section-title">Available schedules</div>
            @foreach($trip->schedules as $s)
            <div class="schedule-row">
                <div>
                    <div class="sched-date">
                        {{ \Carbon\Carbon::parse($s->start_date)->format('d M Y') }}
                        – {{ \Carbon\Carbon::parse($s->end_date)->format('d M Y') }}
                    </div>
                    <div class="sched-sub">
                        Deadline: {{ \Carbon\Carbon::parse($s->booking_deadline)->format('d M') }}
                        &nbsp;•&nbsp; {{ $s->available_seats }} seats
                        @if($s->price_modifier && $s->price_modifier != 1)
                            &nbsp;•&nbsp; {{ $s->price_modifier > 1 ? '+' : '' }}{{ round(($s->price_modifier - 1) * 100) }}%
                        @endif
                    </div>
                </div>
                @php $full = $s->available_seats <= 3; @endphp
                <span class="sched-badge {{ $full ? 'sched-full' : 'sched-open' }}">
                    {{ $full ? 'Almost full' : ucfirst($s->status ?? 'open') }}
                </span>
            </div>
            @endforeach
        </div>
        @endif

      {{-- Assigned guide --}}
      @if($trip->assignedGuide)
      <div class="side-card">
      
          <div class="section-title">Assigned Guide</div>
      
          <div class="guide-row">
      
              <div class="guide-avatar">
                  {{ strtoupper(substr($trip->assignedGuide?->user?->name ?? 'G', 0, 1)) }}
              </div>
      
              <div style="flex:1">
                  <div style="font-size:14px;font-weight:500;">
                      {{ $trip->assignedGuide?->user?->name }}
                  </div>
      
                  <div style="font-size:12px;color:#888">
                      {{ $trip->assignedGuide->years_of_experience ?? 0 }} years experience
                  </div>
      
                  {{-- rating --}}
                  <div style="margin-top:4px;font-size:13px;color:#f59e0b">
                      @php $r = $guideRating ?? 0; @endphp
                      @for($i=1; $i<=5; $i++)
                          <span style="color:{{ $i <= $r ? '#f59e0b' : '#e5e7eb' }}">★</span>
                      @endfor
      
                      <span style="color:#666;font-size:12px;">
                          ({{ number_format($r,1) }})
                      </span>
                  </div>
              </div>
      
              <a href="{{ route('reviews.guide', $trip->assignedGuide->id) }}"
                 style="margin-left:auto;background:#185FA5;color:white;
                 font-size:11px;padding:6px 10px;border-radius:12px;text-decoration:none;">
                  Reviews
              </a>
      
          </div>
      </div>
      @endif

            {{-- All guide assignments --}}
          @if($trip->assignments->count())
    <div style="margin-bottom:1.5rem">
        <div class="section-title">Guide assignments</div>

        @foreach($trip->assignments as $asgn)
            <div class="guide-row">
                <div class="guide-avatar">
                    {{ strtoupper(substr($asgn->guide?->name ?? 'G', 0, 1)) }}
                </div>

                <div>
                    <div style="font-size:14px;font-weight:500;color:#1a1a1a">
                        {{ $asgn->guide?->name ?? 'Guide' }}
                    </div>
                    <div style="font-size:12px;color:#888">
                        {{ ucfirst($asgn->status) }}
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div style="padding:12px 14px;border-radius:10px;background:#f4f4f0;color:#777;font-size:13px;display:flex;align-items:center;gap:8px;">
        <span style="font-size:16px;">👤</span>
        No guide assigned yet
    </div>
@endif



        </div>
    </div>

{{-- ══ PHOTO GALLERY ════════════════════════════════ --}}
@if($trip->images->count())
<div class="photo-gallery-section">

    <div class="photo-gallery-header">
        <h1>Photo Gallery</h1>
        <p>Discover {{ $trip->name }} through stunning imagery</p>
    </div>

    <div class="photo-gallery-grid">
        @foreach($trip->images as $image)
            <img class="pg-photo" src="{{ $image->image_path }}" alt="Trip photo">
        @endforeach
    </div>

</div>
@endif

{{-- ══ IMAGE POPUP ══════════════════════════════════ --}}
<div id="trip-image-popup" class="trip-popup-overlay">
    <span class="trip-close-btn">&times;</span>
    <img id="trip-popup-image" src="" alt="Large View">
    <div class="trip-arrow left">&#10094;</div>
    <div class="trip-arrow right">&#10095;</div>
</div>

</div>

</x-app-layout>

<script>
function toggleDay(header) {
    const chevron = header.querySelector('.day-chevron');
    const body    = header.nextElementSibling;
    const isOpen  = body.classList.contains('open');
    body.style.display = isOpen ? 'none' : 'block';
    body.classList.toggle('open', !isOpen);
    chevron.classList.toggle('open', !isOpen);
}
</script>



<!-- 1. تأكد من وجود روابط Leaflet في الـ Head أو قبل السكريبت -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

@if($coords)
<script>
document.addEventListener("DOMContentLoaded", function() {
    const lat = {{ $coords['latitude'] }};
    const lng = {{ $coords['longitude'] }};
    const address = "{{ $trip->meeting_point_address }}";

    const mapContainer = document.getElementById('trip-map');
    if (!mapContainer) return;

    const map = L.map('trip-map').setView([lat, lng], 16);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    L.marker([lat, lng]).addTo(map)
        .bindPopup(address)
        .openPopup();

    setTimeout(() => { map.invalidateSize(); }, 500);
});
</script>
@endif


<style>
/* 8. ضروري جداً لمنع تداخل تنسيقات الصور مع الخريطة */
#trip-map img {
    max-width: none !important;
    background: none !important;
    display: inline !important;
}
</style>





<script>
document.addEventListener('DOMContentLoaded', function () {

    const imgs     = document.querySelectorAll('.pg-photo');
    const popup    = document.getElementById('trip-image-popup');
    const bigImg   = document.getElementById('trip-popup-image');
    const closeBtn = document.querySelector('.trip-close-btn');
    const leftBtn  = document.querySelector('.trip-arrow.left');
    const rightBtn = document.querySelector('.trip-arrow.right');

    let currentIndex = 0;

    function openPopup(index) {
        currentIndex = index;
        bigImg.src = imgs[currentIndex].src;
        popup.style.display = 'flex';
    }

    imgs.forEach((img, i) => {
        img.addEventListener('click', () => openPopup(i));
    });

    closeBtn.addEventListener('click', () => {
        popup.style.display = 'none';
    });

    popup.addEventListener('click', (e) => {
        if (e.target === popup) {
            popup.style.display = 'none';
        }
    });

    leftBtn.addEventListener('click', () => {
        currentIndex = (currentIndex - 1 + imgs.length) % imgs.length;
        bigImg.src = imgs[currentIndex].src;
    });

    rightBtn.addEventListener('click', () => {
        currentIndex = (currentIndex + 1) % imgs.length;
        bigImg.src = imgs[currentIndex].src;
    });

    document.addEventListener('keydown', (e) => {
        if (popup.style.display !== 'flex') return;

        if (e.key === 'ArrowLeft')  leftBtn.click();
        if (e.key === 'ArrowRight') rightBtn.click();
        if (e.key === 'Escape')     popup.style.display = 'none';
    });

});
</script>
