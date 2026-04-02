<x-app-layout>
    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/aiTrip.css') }}">
    <link rel="stylesheet" href="{{ asset('css/cardetails.css') }}">
    @endpush

    <div class="trip-container">
        @if (session('success'))
            <div class="success-message">✓ {{ session('success') }}</div>
        @endif

        <div class="trip-header">
            <h1>{{ $trip->name }}</h1>
            <div class="trip-meta">
                <div class="trip-meta-item"><strong>📍 Destinations:</strong> <span>{{ $trip->destinations->pluck('name')->join(' • ') ?: $trip->destination?->name }}</span></div>
                <div class="trip-meta-item"><strong>📅 Duration:</strong> <span>{{ $trip->duration_days }} days</span></div>
                <div class="trip-meta-item"><strong>👥 Max Participants:</strong> <span>{{ $trip->max_participants ?? '-' }}</span></div>
                <div class="trip-meta-item"><strong>🤖 Source:</strong> <span>AI + DB Catalog</span></div>
            </div>
        </div>

        <div class="trip-itinerary">
            @foreach($trip->days->sortBy('day_number') as $day)
                @php
                    $cleanTitle = trim((string) $day->title);
                    $cleanTitle = preg_replace('/^day\s*\d+\s*[-:]?\s*/i', '', $cleanTitle);
                @endphp

                <article class="day-card">
                    <header class="day-card-header">
                        <h2>Day {{ $day->day_number }}@if(!empty($cleanTitle)) - {{ $cleanTitle }}@endif</h2>
                    </header>

                    @if(!empty($day->description))
                        <p class="day-description">{{ $day->description }}</p>
                    @endif

                    @if(!empty($day->highlights))
                        <section class="info-section highlights-section">
                            <h3>✨ Highlights</h3>
                            <ul class="tag-list">
                                @foreach($day->highlights as $highlight)
                                    <li>{{ $highlight }}</li>
                                @endforeach
                            </ul>
                        </section>
                    @endif

                    @if($day->hotel)
                        <section class="info-section hotel-section">
                            <h3>Hotel Details :</h3>
                            <div class="detail-grid">
                                <div class="detail-item"><strong>Name:</strong> <span>{{ $day->hotel->name }}</span></div>
                                <div class="detail-item"><strong>Stars:</strong> <span>{{ $day->hotel->stars }} ★</span></div>
                                <div class="detail-item"><strong>Price / Night:</strong> <span>${{ $day->hotel->price_per_night }}</span></div>
                                <div class="detail-item"><strong>Check in / out:</strong> <span>{{ $day->hotel->check_in_time?->format('H:i') ?? '-' }} / {{ $day->hotel->check_out_time?->format('H:i') ?? '-' }}</span></div>
                            </div>

                            @if(!empty($day->hotel->description))
                                <p><strong>About hotel:</strong> {{ $day->hotel->description }}</p>
                            @endif
                            @if(!empty($day->hotel->amenities))
                                <p><strong>Amenities:</strong> {{ implode(', ', $day->hotel->amenities) }}</p>
                            @endif
                            @if(!empty($day->hotel->nearby_landmarks))
                                <p><strong>Nearby landmarks:</strong> {{ $day->hotel->nearby_landmarks }}</p>
                            @endif
                            @if(!empty($day->hotel->policies))
                                <p><strong>Policies:</strong> {{ $day->hotel->policies }}</p>
                            @endif
                        </section>
                    @endif

                    @if($day->activities->isNotEmpty())
                        <section class="info-section activities-section">
                            <h3>Activities :</h3>
                            <div class="activity-list">
                                @foreach($day->activities as $activity)
                                    <div class="activity-card">
                                        <div class="activity-row">
                                            <strong>{{ $activity->activity?->name }}</strong>
                                            <span class="price-pill">${{ $activity->activity?->price }}</span>
                                        </div>
                                        @if($activity->notes)
                                            <p><strong>Notes:</strong> {{ $activity->notes }}</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endif
                </article>
            @endforeach
        </div>

        <div class="trip-actions">
            <a href="{{ route('trips.index') }}" class="btn btn-secondary">← Back to Trips</a>
            <a href="{{ route('trips.index') }}" class="btn btn-secondary" style="background-color: #22c55e;color:white;">Complete Creating --></a>
        </div>
    </div>
</x-app-layout>
