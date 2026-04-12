<x-app-layout>
@push('styles')
<link rel="stylesheet" href="{{ asset('css/tripindex.css') }}">
   <link rel="stylesheet" href="{{ asset('css/transport.css') }}">
@endpush




{{-- ══ HERO ══ --}}
<div class="trips-hero">
    <div class="trips-hero-bg"
         style="background-image: url('{{ asset('images/trip.jpg') }}')">
    </div>
    <div class="trips-hero-overlay"></div>
    <div class="trips-hero-content">
        <h1>My Trips</h1>
        <p>Explore and manage all your travel experiences</p>
        <form method="GET" action="{{ route('trips.index') }}" class="trips-hero-search">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search by name, destination, or description..." />
            <button type="submit">Search</button>
        </form>
    </div>
</div>

{{-- ══ MAIN ══ --}}
<div class="trips-page-wrap">

    @if(session('success'))
        <div class="trips-success">{{ session('success') }}</div>
    @endif

    {{-- Filters --}}
    <form method="GET" action="{{ route('trips.index') }}" class="trips-filters">
        <div class="filter-group">
            <label>Category</label>
            <select name="license_category">
                <option value="">All</option>
                <option value="A" @selected(request('license_category')=='A')>A</option>
                <option value="B" @selected(request('license_category')=='B')>B</option>
            </select>
        </div>
        <div class="filter-group">
            <label>Status</label>
            <select name="status">
                <option value="">All</option>
                <option value="draft"      @selected(request('status')=='draft')>Draft</option>
                <option value="published"  @selected(request('status')=='published')>Published</option>
            </select>
        </div>
        <div class="filter-group">
            <label>Destination</label>
            <input type="text" name="destination"
                   value="{{ request('destination') }}" placeholder="e.g. Paris">
        </div>
        <div class="filter-actions">
            <button type="submit" class="btn-filter">Filter</button>
            <a href="{{ route('trips.index') }}" class="btn-reset">Reset</a>
        </div>
    </form>

    {{-- Header --}}
    <div class="trips-section-header">
        <h2>All Trips</h2>
        <span>{{ $trips->count() }} trips found</span>
    </div>

    {{-- Cards --}}
    <div class="trips-grid">
        @foreach($trips as $trip)
        <div class="trip-card">

            {{-- Image --}}
            <div class="trip-card-img">
                @if($trip->images->count())
                    <img src="{{ $trip->images->first()->image_path }}" alt="{{ $trip->name }}">
                @else
                    <div class="trip-img-placeholder">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none"
                             stroke="#185FA5" stroke-width="1.5">
                            <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                            <polyline points="9 22 9 12 15 12 15 22"/>
                        </svg>
                        <span>No image uploaded</span>
                    </div>
                @endif

              
              <span class="trip-status-dot trip-status-{{$trip->status }}">
                 @if($trip->schedules->first())
                    <span class="status-pill">
                   {{ ucfirst($trip->schedules->first()->status) }}
                   </span>
                @endif
               </span>
            </div>

            {{-- Body --}}
            <div class="trip-card-body">
                <div class="trip-card-title">{{ $trip->name }}</div>
                <div class="trip-card-desc">
                    {{ Str::limit($trip->ai_prompt ?? $trip->description, 100) }}
                </div>
                <div class="trip-card-meta">
                    @if($trip->destination)
                        <span class="meta-tag">📍 {{ $trip->destination->name }}</span>
                    @endif
                    @if($trip->max_participants)
                        <span class="meta-tag">👥 {{ $trip->max_participants }} travelers</span>
                    @endif
                </div>
            </div>

            {{-- Footer --}}
            <div class="trip-card-footer">
                <span class="trip-card-days">{{ $trip->duration_days }} days</span>
                @if($trip->is_ai_generated)
                    <a href="{{ route('user.trips.show', $trip) }}" class="view-btn">View Details</a>
                @else
                    <a href="{{ route('manual.show', $trip->id) }}" class="view-btn">View Details</a>
                @endif
            </div>

        </div>
        @endforeach
    </div>

</div>
</x-app-layout>