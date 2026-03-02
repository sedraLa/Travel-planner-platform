<x-app-layout>
    <div class="max-w-5xl mx-auto py-10">
        <h1 class="text-2xl font-bold mb-6">Pending Reservations</h1>

        @foreach($reservations as $reservation)
            <div class="bg-white rounded-lg shadow p-5 mb-4">
                <p><strong>Status:</strong> {{ $reservation->status }}</p>
                <p><strong>User:</strong> {{ $reservation->user?->full_name }}</p>
                <p><strong>Vehicle:</strong> {{ $reservation->vehicle?->car_model }}</p>
                <p><strong>Pickup:</strong> {{ $reservation->pickup_location }}</p>
                <p><strong>Dropoff:</strong> {{ $reservation->dropoff_location }}</p>
            </div>
        @endforeach
    </div>
</x-app-layout>