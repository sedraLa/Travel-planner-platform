<x-app-layout>
    @push('styles')
    <link rel="stylesheet" href="{{asset('css/vehicles.css')}}">
<link rel="stylesheet" href="{{ asset('css/transport.css') }}">
<link rel="stylesheet" href="{{ asset('css/aiTrip.css') }}">
    @endpush

    <div class="trip-container">
       
        @if (session('success'))
            <div class="success-message">
                ✓ {{ session('success') }}
            </div>
        @endif
        <div class="trip-header">
            <h1>{{ $trip->name }}</h1>
            <div class="trip-meta">
                <div class="trip-meta-item">
                    <strong>📅 Duration:</strong>
                    <span>{{ $trip->duration ?? \Carbon\Carbon::parse($trip->start_date)->diffInDays(\Carbon\Carbon::parse($trip->end_date)) + 1 }} days</span>
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
        </div>

       
        <div class="trip-itinerary">
            {!! Str::markdown($trip->ai_itinerary) !!}
        </div>

      
        <div class="trip-actions">
            <a href="{{ route('trips.index') }}" class="btn btn-secondary">← Back to Trips</a>
            <button class="btn btn-primary" onclick="window.print()">🖨️ Print Itinerary</button>
           
        </div>
    </div>

</x-app-layout>