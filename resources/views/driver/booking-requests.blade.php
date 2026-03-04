<x-app-layout>
    @push('styles')
<link rel="stylesheet" href="{{asset('css/vehicles.css')}}">
<link rel="stylesheet" href="{{ asset('css/transport.css') }}">
@endpush
    <div class="max-w-5xl mx-auto py-10">
        <h1 class="text-2xl font-bold mb-6">Booking Requests</h1>

        
        {{-- Success messages --}}
        @if (session('success'))
            <div class="mb-6 px-4 py-3 bg-green-100 border border-green-200 text-green-800 rounded-lg">
                {{ session('success') }}
            </div>
        @endif
        
        @foreach($requests as $request)
            <div class="bg-white rounded-lg shadow p-5 mb-4">
                <p><strong>Status:</strong> {{ $request->status }}</p>
                <p><strong>Pickup:</strong> {{ $request->reservation->pickup_location }}</p>
                <p><strong>Dropoff:</strong> {{ $request->reservation->dropoff_location }}</p>
                <p><strong>Expires at:</strong> {{ $request->expires_at }}</p>

                @if($request->status === 'pending')
                    <div class="flex gap-3 mt-3">
                        <form method="POST" action="{{ route('driver.booking-requests.accept', $request) }}">@csrf<button class="px-4 py-2 bg-green-600 text-white rounded">Accept</button></form>
                        <form method="POST" action="{{ route('driver.booking-requests.reject', $request) }}">@csrf<button class="px-4 py-2 bg-red-600 text-white rounded">Reject</button></form>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</x-app-layout>