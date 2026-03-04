<x-app-layout>
    @push('styles')
<link rel="stylesheet" href="{{asset('css/vehicles.css')}}">
<link rel="stylesheet" href="{{ asset('css/transport.css') }}">
@endpush
    <div class="max-w-4xl mx-auto py-12">
        <h1 class="text-3xl font-bold mb-6">Driver assigned</h1>

        <div class="bg-white rounded-xl shadow p-6 space-y-3">
            <p><strong>Driver:</strong> {{ $reservation->driver?->user?->full_name ?? 'N/A' }}</p>
            <p><strong>Vehicle:</strong> {{ $reservation->vehicle?->car_model ?? 'N/A' }}</p>
            <p><strong>Pickup:</strong> {{ $reservation->pickup_location }}</p>
            <p><strong>Dropoff:</strong> {{ $reservation->dropoff_location }}</p>
            <p><strong>Date:</strong> {{ optional($reservation->pickup_datetime)->format('Y-m-d H:i') }}</p>
        </div>

        <a href="{{ route('vehicle.reservation', $reservation) }}" class="mt-8 inline-block bg-blue-600 text-white px-6 py-3 rounded-lg">
            Reserve
        </a>
    </div>
</x-app-layout>