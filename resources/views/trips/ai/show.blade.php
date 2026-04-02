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
                <div class="trip-meta-item"><strong>📍 Destination:</strong> <span>{{ $trip->destination?->name }}</span></div>
                <div class="trip-meta-item"><strong>📅 Duration:</strong> <span>{{ $trip->duration_days }} days</span></div>
                <div class="trip-meta-item"><strong>👥 Max Participants:</strong> <span>{{ $trip->max_participants ?? '-' }}</span></div>
                <div class="trip-meta-item"><strong>🤖 Source:</strong> <span>AI + DB Catalog</span></div>
            </div>
        </div>

        <div class="trip-itinerary">
            @foreach($trip->days->sortBy('day_number') as $day)
                <h2>Day {{ $day->day_number }} - {{ $day->title }}</h2>
                <p>{{ $day->description }}</p>

                @if(!empty($day->highlights))
                    <ul>
                        @foreach($day->highlights as $highlight)
                            <li>{{ $highlight }}</li>
                        @endforeach
                    </ul>
                @endif

                @if($day->hotel)
                    <p><strong>Hotel:</strong> {{ $day->hotel->name }} - {{$day->hotel->stars}} Stars / price per night:  {{ $day->hotel->price_per_night }}$ </p> <br>
                @endif

                @if($day->activities->isNotEmpty())
                    <ul>
                        @foreach($day->activities as $activity)
                            <li>
                                <strong>{{ $activity->activity?->name }}</strong> - {{ $activity->activity?->price }}$ <br>
                                
                                @if($activity->notes)
                                <strong>Notes:</strong>
                                    - {{ $activity->notes }}
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
