@push('styles')
<link rel="stylesheet" href="{{asset('css/tripdetails.css')}}">
@endpush
@php
    use Carbon\Carbon;
     use App\Enums\UserRole;
     $isAdmin = auth()->user()?->role === \App\Enums\UserRole::ADMIN->value;
@endphp

<x-app-layout>
<div class="hero">
  <div class="hero-img-wrap">
   <img id="heroImg"class="hero-img"src="{{ asset('storage/' . $activity->image) }}"alt="activity ph"onerror="this.style.display='none'">

  </div>
  <div class="hero-pattern"></div>
  <div class="hero-overlay"></div>


  <input type="file" id="imgUpload" accept="image/*" style="display:none;">

  <div class="hero-content">
    <div class="hero-left">
      <div class="hero-eyebrow">
        <span class="tag tag-accent">{{ ucfirst($activity->category) }}</span>
        <span class="tag tag-gold">⭐{{ number_format($activity->average_rating ?? 0, 1) }}</span>
        <span class="tag tag-outline">
          <span class="avail-dot" style="background:#9fe1cb;"></span>
         {{$activity->availability}}
        </span>
      </div>
      <h1 class="hero-title"> <br>{{$activity->name}}</h1>
      <div class="hero-dest">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
          {{$activity->address}}
      </div>

    </div>
    <div class="hero-price-box">
      <div class="price-label">Start from </div>
      <div class="price-amount">${{$activity->price}}</div>
      <div class="price-per">For Per Person </div>
        @if (Auth::user()->role === UserRole::USER->value)
      <a href="{{ route('activity.reservations.form', $activity->id) }}" class="book-btn">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
        <rect x="3" y="4" width="18" height="18" rx="2"/>
        <line x1="16" y1="2" x2="16" y2="6"/>
        <line x1="8" y1="2" x2="8" y2="6"/>
        <line x1="3" y1="10" x2="21" y2="10"/>
    </svg>
    Book Now
</a>
 @endif

    </div>
  </div>
</div>

{{-- ══ AI ACTIVITY ASSISTANT ═════════════════════════ --}}
<div class="ai-box">

    <div class="ai-box-bg"></div>

    <div class="ai-box-content">

        <div class="ai-box-header">
            <div class="ai-icon">✨</div>

            <div>
                <div class="ai-title">
                    Smart Activity Assistant
                </div>
                <div class="ai-sub">
                    Ask anything about this activity
                </div>
            </div>
        </div>

        <div class="ai-actions">

            <button onclick="askActivityAI('tips')" class="ai-btn">
                💡 Tips
            </button>

            <button onclick="askActivityAI('safety')" class="ai-btn">
                ⚠️ Safety
            </button>

            <button onclick="askActivityAI('what_to_bring')" class="ai-btn">
                🎒 What to bring
            </button>

            <button onclick="askActivityAI('best_time')" class="ai-btn">
                🕒 Best time
            </button>

        </div>

        <div class="ai-chat">
            <input id="activity-ai-input" placeholder="Ask anything..." />
            <button onclick="sendActivityQuestion()">Ask</button>
        </div>

        <div id="activity-ai-response" class="ai-response"></div>

    </div>

</div>

<!-- STICKY NAV -->
<nav class="sticky-nav">
  <a href="#overview"   class="nav-tab active" data-target="overview">Overview</a>
  <a href="#highlights" class="nav-tab"         data-target="highlights">Features  </a>
  <a href="#details"    class="nav-tab"         data-target="details">Details</a>
  <a href="#amenities"  class="nav-tab"         data-target="amenities">Facilities</a>
  @if($hasPaidReservation)
  <a href="#contact" class="nav-tab" data-target="contact">
        Contact
    </a>
@endif
</nav>

<!-- MAIN -->
<div class="main-wrap">
  <div class="main-col">

    <!-- STATS -->
    <div class="stats-row">
      <div class="stat-item">
        <div class="stat-icon">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1a5fd4" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <div class="stat-number">{{$activity->duration}}</div>
        <div class="stat-desc">{{$activity->duration_unit}}</div>
      </div>
      <div class="stat-item">
        <div class="stat-icon">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#0e99c0" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
        </div>
        <div class="stat-number">Destination</div>
        <div class="stat-desc"> {{$activity->destination->name}}  </div>
      </div>
      <div class="stat-item">
        <div class="stat-icon">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#3b82c4" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
        </div>
        <div class="stat-number">⭐ {{ number_format($activity->average_rating ?? 0, 1) }}</div>
        <div class="stat-desc">({{ $reviewsCount }}) Reviews</div>
      </div>
    </div>

    <!-- OVERVIEW -->
    <div class="section" id="overview">
      <div class="section-head">
        <div class="section-icon icon-gold">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#3b82c4" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        </div>
        <h2 class="section-title">About The Activity </h2>
      </div>
      <div class="divider"></div>
      <p class="description-text">
       {{$activity->description}}
      </p>
      <div class="category-strip">
        <span class="tag tag-accent">{{ ucfirst($activity->category) }}</span>

      </div>
    </div>

    <!-- HIGHLIGHTS -->
    <div class="section" id="highlights">
      <div class="section-head">
        <div class="section-icon icon-red">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#1a5fd4" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
        </div>
        <h2 class="section-title">Highlights Features </h2>
      </div>
      <div class="divider"></div>
      <ul class="highlights-list">
    @forelse($activity->highlights as $highlight)
        <li class="highlight-item">
            ⭐ {{ $highlight->title }}
        </li>
    @empty
        <li class="text-muted">No highlights available</li>
    @endforelse
</ul>
    </div>

    <!-- REVIEWS SECTION -->
<div class="section" id="reviews">
    <div class="section-head">
      <div class="section-icon icon-blue">
        ⭐
      </div>
      <h2 class="section-title">User Reviews</h2>
    </div>

    <div class="divider"></div>

    @if($reviews->count())

      @foreach($reviews->take(3) as $review)
        <div style="margin-bottom:15px; padding:12px; border:1px solid #eee; border-radius:10px;">

          <div style="font-weight:600;">
            {{ $review->user->name }}
          </div>

          <div style="color:#f5a623; font-size:14px;">
            ⭐ {{ $review->rating }}
          </div>

          @if($review->review)
            <div style="margin-top:5px; font-size:13px; color:#555;">
              {{ $review->review }}
            </div>
          @endif

        </div>
      @endforeach

      {{-- زر مشاهدة الكل --}}
      @if($reviews->count() > 3)
        <div style="text-align:center; margin-top:10px;">
          <a href="{{ route('activities.reviews.index', $activity->id) }}" class="book-btn" style="padding:8px 14px;">
            See all {{ $reviewsCount }} reviews
          </a>
        </div>
      @endif

    @else
      <p style="color:#888;">No reviews yet</p>
    @endif
  </div>

    <!-- REQUIREMENTS -->
    <div class="section">
      <div class="section-head">
        <div class="section-icon icon-gold">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#3b82c4" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        </div>
        <h2 class="section-title">Requirements and Conditions</h2>
      </div>
      <div class="divider"></div>
      <div class="requirements-box">
         {{$activity->requirements}}
      </div>
    </div>

    <!-- DIFFICULTY -->
    <!-- DIFFICULTY -->
<div class="section difficulty-section" id="details">
  <div class="section-head">
    <div class="section-icon icon-green">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0e99c0" stroke-width="2">
        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
      </svg>
    </div>
    <h2 class="section-title">Difficulty level</h2>
  </div>

  <div class="divider"></div>

  @php
      $level = $activity->difficulty_level;
  @endphp

  <div class="diff-levels">
    <div class="diff-bar {{ $level == 'easy' ? 'active-easy' : '' }}"></div>
    <div class="diff-bar {{ $level == 'moderate' ? 'active-moderate' : '' }}"></div>
    <div class="diff-bar {{ $level == 'hard' ? 'active-hard' : '' }}"></div>
  </div>

  <div class="diff-labels">
    <span class="{{ $level == 'easy' ? 'active-text' : '' }}">
      {{ $level == 'easy' ? 'Easy ✓' : 'Easy' }}
    </span>

    <span class="{{ $level == 'moderate' ? 'active-text' : '' }}">
      {{ $level == 'moderate' ? 'Moderate ✓' : 'Moderate' }}
    </span>

    <span class="{{ $level == 'hard' ? 'active-text' : '' }}">
      {{ $level == 'hard' ? 'Hard ✓' : 'Hard' }}
    </span>
  </div>


</div>
    <!-- POLICIES -->
    <div class="section">
      <div class="section-head">
        <div class="section-icon icon-blue">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#1a5fd4" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        </div>
        <h2 class="section-title">Activity policies </h2>
      </div>
      <div class="divider"></div>
      <table class="info-table">
        <tr>
          <td><div class="col-label">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
           Reservation required in advance
          </div></td>
          <td><div class="col-value bool-yes">
            {{ $activity->requires_booking ? 'Yes' : 'No' }}
          </div></td>
        </tr>
        <tr>
          <td><div class="col-label">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
           Family-friendly
          </div></td>
          <td><div class="col-value bool-yes">
          {{ $activity->family_friendly  }}
          </div></td>
        </tr>
        <tr>
          <td><div class="col-label">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 5.172C10 3.782 8.423 2.379 6.5 3c-2.823.893-4.956 3.57-4 6.5.956 2.929 2.542 4.63 4.5 5.5 4 1.8 8.5-.5 8.5-4 0-2.5-2-5-3.5-4.828A2 2 0 0010 5.172z"/></svg>
             Pets
          </div></td>
          <td><div class="col-value bool-no">
             {{ $activity->pets_allowed ? 'Yes' : 'No'}}
          </div></td>
        </tr>

      </table>
    </div>

    <!-- AMENITIES -->
    <div class="section" id="amenities">
      <div class="section-head">
        <div class="section-icon icon-green">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0e99c0" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
        </div>
        <h2 class="section-title">Facilities and services </h2>
      </div>
      <div class="divider"></div>
      <div class="amenities-grid">
        @forelse($activity->amenities ?? [] as $amenity)
          <span class="amenity-pill">
          <span class="amenity-dot"></span>
           {{ $amenity }}
         </span>
       @empty
        <span class="text-muted">No facilities available</span>
          @endforelse
      </div>
    </div>

  </div>

  <!-- SIDEBAR -->
  <aside class="sidebar">

    <!-- Dates -->
    <div class="side-card">
      <div class="side-card-head">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#1a5fd4" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
       Activity timings
      </div>
      <div class="side-card-body">
        <div class="date-range">
          <div class="date-box">
            <div class="date-label">Strat</div>
            <div class="date-value">{{ $activity->start_date ? Carbon::parse($activity->start_date)->translatedFormat('d M') : '-' }} </div>
            <div style="font-size:11px; color:var(--muted);">{{ $activity->start_date ? Carbon::parse($activity->start_date)->format('Y') : '' }}</div>
          </div>
          <div class="date-arrow">←</div>
          <div class="date-box">
            <div class="date-label">End</div>
            <div class="date-value">{{ $activity->end_date ? Carbon::parse($activity->end_date)->translatedFormat('d M') : '-' }} </div>
            <div style="font-size:11px; color:var(--muted);">{{ $activity->end_date ? Carbon::parse($activity->end_date)->format('Y') : '' }}</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Contact -->
    <div class="side-card" id="contact">
      <div class="side-card-head">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#0e99c0" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.81a19.79 19.79 0 01-3.07-8.63A2 2 0 012 0h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L6.09 7.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 14h-2 2v2.92z"/></svg>
       Contact information
      </div>
      <div class="side-card-body" style="padding-top:0.5rem;">

          @if($isAdmin||$hasPaidReservation ||!$activity->requires_booking)
        <div class="contact-row">
          <div class="contact-icon">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#1a5fd4" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.81a19.79 19.79 0 01-3.07-8.63A2 2 0 012 0h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L6.09 7.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 14h-2 2v2.92z"/></svg>
          </div>
          <div>
            <div class="contact-label"> Phone Number</div>
            <div class="contact-value">{{ $activity->contact_number }}</div>
          </div>
        </div>
        <div class="contact-row">
          <div class="contact-icon">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#0e99c0" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
          </div>
          <div>
            <div class="contact-label">Email </div>
            <div class="contact-value">{{ $activity->contact_email }}</div>
          </div>
        </div>
        @else
         <div style="padding:12px; font-size:13px; color:#888;">
            🔒 Pay to unlock contact details
         </div>

    @endif
      </div>
    </div>

    <!-- Map -->
@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #activity-map {
        height: 250px !important;
        width: 100% !important;
        border-radius: 12px;
        z-index: 1;
        background: #e5e3df;
    }
    .leaflet-container {
        height: 100%;
        width: 100%;
    }
</style>
@endpush

<!-- Map -->
<div class="side-card">
  <div class="side-card-head">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#3b82c4" stroke-width="2">
      <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/>
      <circle cx="12" cy="10" r="3"/>
    </svg>
    Location
  </div>

  <div class="side-card-body" style="padding: 10px;">
    <div id="map-wrapper" style="width: 100%; height: 200px; border-radius: 12px; overflow: hidden; position: relative; background: #f0f0f0;">
      @if($coords && isset($coords['latitude']) && $coords['latitude'] != 0)
        <div id="activity-map" style="width: 100%; height: 100%; position: absolute; top: 0; left: 0; z-index: 1;"></div>
      @else
        <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; text-align: center; padding: 20px;">
          <span style="font-size: 24px; margin-bottom: 8px;">📍</span>
          <span style="font-size: 12px; color: #666; font-weight: 600;">{{ $activity->address }}</span>
        </div>
      @endif
    </div>
  </div>
</div>

    <!-- CTA -->
     @if (Auth::user()->role === UserRole::USER->value)
    <div style="background:var(--ink); border-radius:6px; padding:1.5rem; text-align:center;">
      <p style="color:rgba(255,255,255,0.6); font-size:12px; margin-bottom:6px; letter-spacing:0.05em;">Don’t miss this unique experience</p>
      <p style="color:#fff; font-family:'Cairo',sans-serif; font-size:16px; font-weight:700; margin-bottom:1rem;">Reserve your spot now  </p>
      <a href="{{ route('activity.reservations.form', $activity->id) }}" class="book-btn" style="font-size:15px; padding:13px;">${{$activity->price}}only</a>
    </div>
  @endif
  </aside>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

@if($coords && isset($coords['latitude'] ) && $coords['latitude'] != 0)
<script>
  document.addEventListener("DOMContentLoaded", function() {
    if (typeof L === 'undefined') return;

    const lat = {{ $coords['latitude'] }};
    const lng = {{ $coords['longitude'] }};
    const address = "{{ addslashes($activity->address) }}";
    const map = L.map('activity-map', {
        center: [lat, lng],
        zoom: 15,
        zoomControl: true
    });

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    } ).addTo(map);

    L.marker([lat, lng]).addTo(map)
        .bindPopup(address)
        .openPopup();
    setTimeout(function() {
        map.invalidateSize();
    }, 500);

    setTimeout(function() {
        map.invalidateSize();
    }, 1500);
  });
</script>
@endif

</x-app-layout>

<script>
  // Image upload
  document.getElementById('imgUpload').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function(ev) {
      const img = document.getElementById('heroImg');
      const ph  = document.getElementById('heroPlaceholder');
      img.src = ev.target.result;
      img.style.display = 'block';
      ph.style.display  = 'none';
    };
    reader.readAsDataURL(file);
  });

  // Sticky nav
  const tabs     = document.querySelectorAll('.nav-tab');
  const sections = ['overview','highlights','details','amenities','contact'];

  tabs.forEach(tab => {
    tab.addEventListener('click', e => {
      e.preventDefault();
      tabs.forEach(t => t.classList.remove('active'));
      tab.classList.add('active');
      const el = document.getElementById(tab.dataset.target);
      if (el) window.scrollTo({ top: el.offsetTop - 80, behavior: 'smooth' });
    });
  });

  window.addEventListener('scroll', () => {
    let current = '';
    sections.forEach(id => {
      const el = document.getElementById(id);
      if (el && window.scrollY >= el.offsetTop - 120) current = id;
    });
    tabs.forEach(tab => tab.classList.toggle('active', tab.dataset.target === current));
  });

  // Scroll animations
  const observer = new IntersectionObserver(
    entries => entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('visible'); }),
    { threshold: 0.1 }
  );
  document.querySelectorAll('.section').forEach(s => observer.observe(s));
</script>

<script>
    function askActivityAI(type) {
        const presetQuestions = {
            tips: "Give me practical tips to enjoy this activity.",
            safety: "What safety advice should I follow for this activity?",
            what_to_bring: "What should I bring for this activity?",
            best_time: "What is the best time to do this activity?"
        };
        const question = presetQuestions[type] ?? "Give me useful planning advice for this activity.";
        const box = document.getElementById('activity-ai-response');
        box.style.display = 'block';
        box.innerHTML = "Thinking...";

        fetch("{{ route('ai.activity.ask') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                entity_id: {{ $activity->id }},
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
            .then(data => {
                box.innerHTML = data.answer;
            })
            .catch((error) => {
                box.innerHTML = error.message || "Something went wrong. Please try again.";
            });
    }

    function sendActivityQuestion() {
        const input = document.getElementById('activity-ai-input').value;
        const box = document.getElementById('activity-ai-response');

        if (!input) return;

        box.style.display = 'block';
        box.innerHTML = "Thinking...";

        fetch("{{ route('ai.activity.ask') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                entity_id: {{ $activity->id }},
                question: input
            })
        })
        .then(async (res) => {
            const data = await res.json();
            if (!res.ok) {
                throw new Error(data.answer || "Could not get AI response.");
            }
            return data;
        })
        .then(data => {
            box.innerHTML = data.answer;
        })
        .catch((error) => {
            box.innerHTML = error.message || "Something went wrong. Please try again.";
        });
    }
    </script>
