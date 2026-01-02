<x-app-layout>
    @push('styles')
    <link rel="stylesheet" href="{{asset('css/vehicles.css')}}">
    <link rel="stylesheet" href="{{ asset('css/transport.css') }}">
    <style>
        
        .trip-container { max-width: 1400px; margin: 0 auto; padding: 30px 20px; background: white; border-radius: 10px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); }
        .trip-header { border-bottom: 3px solid #7f53ac; padding-bottom: 20px; margin-bottom: 30px; margin-top:30px; }
        .trip-header h1 { color: #333; font-size: 28px; margin: 0 0 10px 0; }
        .trip-meta { display: flex; gap: 30px; flex-wrap: wrap; color: #666; font-size: 14px; }
        .trip-meta-item { display: flex; align-items: center; gap: 8px; }
        .trip-meta-item strong { color: #333; }
        .trip-itinerary { line-height: 1.8; color: #333; margin-top: 20px; }
        .trip-itinerary h2 { color: #647dee; font-size: 20px; margin-top: 25px; margin-bottom: 12px; }
        .trip-itinerary h3 { color: #8b7bb8; font-size: 16px; margin-top: 18px; margin-bottom: 10px; }
        .trip-itinerary ul { margin: 10px 0; padding-left: 25px; }
        .trip-itinerary li { margin: 5px 0; }
        .trip-actions { margin-top: 40px; padding-top: 20px; border-top: 1px solid #e0e0e0; display: flex; gap: 15px; flex-wrap: wrap; }
        .btn { padding: 12px 24px; border: none; border-radius: 5px; font-size: 14px; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-block; transition: all 0.3s ease; }
        .btn-primary { background: linear-gradient(135deg, #7f53ac, #647dee); color: white; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 12px rgba(127, 83, 172, 0.3); }
        .btn-secondary { background: #e0e0e0; color: #333; }
        .btn-secondary:hover { background: #d0d0d0; }
        .success-message { background-color: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #c3e6cb; }
    </style>
    @endpush

    <div class="trip-container">
       
        @if (session('success'))
            <div class="success-message">
                ✓ {{ session('success') }}
            </div>
        @endif

        
        <div class="trip-header">
            <h1>{{ $trip->name }}</h1>
            @if($trip->destination_name)
                <h3 class="text-xl text-purple-700 mt-1 mb-3">Destination: {{ $trip->destination_name }}</h3>
            @endif
            <div class="trip-meta">
                <div class="trip-meta-item">
                    <strong>📅 Duration:</strong>
                    <span>{{ \Carbon\Carbon::parse($trip->start_date)->diffInDays(\Carbon\Carbon::parse($trip->end_date)) + 1 }} days</span>
                </div>
                <div class="trip-meta-item">
                    <strong>👥 Travelers:</strong>
                    <span>{{ $trip->travelers_number }}</span>
                </div>
                @if ($trip->budget)
                    <div class="trip-meta-item">
                        <strong>💰 Budget:</strong>
                        <span>${{ number_format($trip->budget, 2) }}</span>
                    </div>
                @endif
                <div class="trip-meta-item">
                    <strong>📍 Dates:</strong>
                    <span>{{ \Carbon\Carbon::parse($trip->start_date)->format('M d') }} - {{ \Carbon\Carbon::parse($trip->end_date)->format('M d, Y') }}</span>
                </div>
            </div>

            @if($trip->flight_number)
                <div class="trip-meta mt-3">
                    <div class="trip-meta-item"><strong>✈️ Airline:</strong> {{ $trip->airline }}</div>
                    <div class="trip-meta-item"><strong>Flight #:</strong> {{ $trip->flight_number }}</div>
                    <div class="trip-meta-item"><strong>From:</strong> {{ $trip->departure_airport ?? '-' }}</div>
                    <div class="trip-meta-item"><strong>To:</strong> {{ $trip->arrival_airport ?? '-' }}</div>
                    @if($trip->departure_time)<div class="trip-meta-item"><strong>Departure:</strong> {{ $trip->departure_time }}</div>@endif
                    @if($trip->arrival_time)<div class="trip-meta-item"><strong>Arrival:</strong> {{ $trip->arrival_time }}</div>@endif
                </div>
            @endif
        </div>

       
        <div class="trip-itinerary">
            @foreach($trip->days as $day)
                <h2>Day {{ $day->day_number }}</h2>
                <p><strong>Hotel:</strong> {{ $day->hotel->name ?? $day->custom_hotel ?? 'Not assigned' }}</p>
                @if($day->activities->count())
                    <p><strong>Activities:</strong></p>
                    <ul>
                        @foreach($day->activities as $activity)
                            <li>{{ $activity->activity->name ?? $activity->custom_activity }}</li>
                        @endforeach
                    </ul>
                @endif
            @endforeach
        </div>

        
        <div class="trip-actions">
            <a href="{{ route('trips.index') }}" class="btn btn-secondary">← Back to Trips</a>
            <button class="btn btn-primary" onclick="window.print()">🖨️ Print Itinerary</button>
        </div>
    </div>
</x-app-layout>
