<x-app-layout>
    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/aiTrip.css') }}">
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
                <h2>Day {{ $day->day_number }}@if(!empty($cleanTitle)) - {{ $cleanTitle }}@endif</h2>
                <p>{{ $day->description }}</p>

                @if(!empty($day->highlights))
                    <ul>
                        @foreach($day->highlights as $highlight)
                            <li>{{ $highlight }}</li>
                        @endforeach
                    </ul>
                @endif

                @if($day->hotel)
                    <p><strong>Hotel:</strong> {{ $day->hotel->name }} — {{ $day->hotel->stars }} Stars / ${{ $day->hotel->price_per_night }} per night</p>
                    @if(!empty($day->hotel->description))
                        <p><strong>About hotel:</strong> {{ $day->hotel->description }}</p>
                    @endif
                    @if(!empty($day->hotel->amenities))
                        <p><strong>Amenities:</strong> {{ implode(', ', $day->hotel->amenities) }}</p>
                    @endif
                    @if($day->hotel->check_in_time || $day->hotel->check_out_time)
                        <p><strong>Check in/out:</strong> {{ $day->hotel->check_in_time?->format('H:i') ?? '-' }} / {{ $day->hotel->check_out_time?->format('H:i') ?? '-' }}</p>
                    @endif
                    @if(!empty($day->hotel->nearby_landmarks))
                        <p><strong>Nearby landmarks:</strong> {{ $day->hotel->nearby_landmarks }}</p>
                    @endif
                    @if(!empty($day->hotel->policies))
                        <p><strong>Policies:</strong> {{ $day->hotel->policies }}</p>
                    @endif
                    <br>
                @endif

                @if($day->activities->isNotEmpty())
                    <ul>
                        @foreach($day->activities as $activity)
                            <li>
                                <strong>{{ $activity->activity?->name }}</strong> - {{ $activity->activity?->price }}$ <br>
                                @if($activity->notes)
                                    <strong>Notes:</strong> {{ $activity->notes }}
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            @endforeach
        </div>

        <div class="trip-actions">
            <a href="{{ route('trips.index') }}" class="btn btn-secondary">← Back to Trips</a>
        </div>
    </div>
</x-app-layout>
