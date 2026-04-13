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
        <h1>All Trips</h1>
        <p>Explore and manage all your travel experiences</p>
        <form method="GET" action="{{ route('user.trips.index') }}" class="trips-hero-search">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search by name, destination" />
            <button type="submit">Search</button>
        </form>
    </div>
</div>

{{-- ══ MAIN ══ --}}
<div class="trips-page-wrap">

    {{-- Filters --}}
    <form method="GET" action="{{ route('user.trips.index') }}">
        <div class="trips-filters">
            <div class="filter-group">
                <label>Category</label>
                <select name="category">
                    <option value="">All</option>
                    @foreach($categories as $category)
                        <option value="{{ $category }}" @selected(request('category') == $category)>
                            {{ ucfirst($category) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label>Destination</label>
                <select name="destination_id">
                    <option value="">All</option>
                    @foreach($destinations as $destination)
                        <option value="{{ $destination->id }}"
                            @selected(request('destination_id') == $destination->id)>
                            {{ $destination->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label>Number of Passengers</label>
                <input type="number" name="max_participants"
                       value="{{ request('max_participants') }}" step="1">
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn-filter">Filter</button>
                <a href="{{ route('user.trips.index') }}" class="btn-reset">Reset</a>
            </div>
        </div>
    </form>

    {{-- Section Header --}}
    <div class="trips-section-header">
        <h2>All Trips</h2>
        <span>{{ $trips->count() }} trips found</span>
    </div>

    {{-- Cards --}}
    <div class="trips-grid">
        @forelse($trips as $trip)
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

                @if($trip->schedules->first())
                    <span class="trip-status-dot trip-status-{{ $trip->schedules->first()->status }}">
                        {{ ucfirst($trip->schedules->first()->status) }}
                    </span>
                @endif
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

        @empty
        <div class="trips-empty">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none"
                 stroke="#B4B2A9" stroke-width="1.2">
                <circle cx="11" cy="11" r="8"/>
                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                <line x1="11" y1="8" x2="11" y2="14"/>
                <line x1="8" y1="11" x2="14" y2="11"/>
            </svg>
            <h3>No trips available</h3>
            <p>Try adjusting your filters or check back later.</p>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($trips->hasPages())
        <div class="pagination-wrapper">
            {{ $trips->appends(request()->query())->links() }}
        </div>
    @endif

</div>
</x-app-layout>