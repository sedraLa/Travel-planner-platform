<x-app-layout>
    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/flights.css') }}">
    @endpush

    @push('styles')
    <link rel="stylesheet" href="{{asset('css/flight-results.css')}}">
    @endpush

    <div class="relative">
        <div class="main-wrapper">
            <div class="hero-background flight-page">
                <div class="headings">
                    <h1 class="page-title">Flight Search Results</h1>
                </div>
            </div>
            <div class="spacer"></div>
        </div>

        <div class="relative z-10 max-w-5xl mx-auto p-6 flights-container">

            {{-- Outbound flights --}}
            @if (count($outboundFlights) === 0)
                <p class="no-flights-msg">No outbound flights found.</p>
            @else
                <h2 class="section-title">Outbound Flights</h2>
                <div class="flights-list">
                    @foreach ($outboundFlights as $flight)
                        <div class="flight-card">
                            <div class="flight-header">
                                <span class="carrier">Carrier: {{ $flight['carrierCode'] }}</span>
                                <span class="flight-number">Flight #: {{ $flight['flightNumber'] }}</span>
                            </div>
                            <div class="flight-info">
                                <p><strong>From:</strong> {{ $flight['from'] }} at <span class="time">{{ $flight['departureTime'] }}</span></p>
                                <p><strong>To:</strong> {{ $flight['to'] }} at <span class="time">{{ $flight['arrivalTime'] }}</span></p>
                            </div>
                            <div class="price">Price: ${{ $flight['price'] }}</div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Inbound flights --}}
            @if ($tripType === 'round')
                @if (count($inboundFlights) === 0)
                    <p class="no-flights-msg">No return flights found.</p>
                @else
                    <h2 class="section-title">Return Flights</h2>
                    <div class="flights-list">
                        @foreach ($inboundFlights as $flight)
                            <div class="flight-card">
                                <div class="flight-header">
                                    <span class="carrier">Carrier: {{ $flight['carrierCode'] }}</span>
                                    <span class="flight-number">Flight #: {{ $flight['flightNumber'] }}</span>
                                </div>
                                <div class="flight-info">
                                    <p><strong>From:</strong> {{ $flight['from'] }} at <span class="time">{{ $flight['departureTime'] }}</span></p>
                                    <p><strong>To:</strong> {{ $flight['to'] }} at <span class="time">{{ $flight['arrivalTime'] }}</span></p>
                                </div>
                                <div class="price">Price: ${{ $flight['price'] }}</div>
                            </div>
                        @endforeach
                    </div>
                @endif
            @endif

            <div class="back-btn-wrapper">
                <a href="{{ route('flights.search') }}" class="btn-back">Back to search</a>
            </div>
        </div>
    </div>
</x-app-layout>
