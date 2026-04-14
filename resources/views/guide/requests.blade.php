<x-app-layout>
    @push('styles')
    <link rel="stylesheet" href="{{asset('css/vehicles.css')}}">
    <link rel="stylesheet" href="{{ asset('css/transport.css') }}">
    @endpush
    <div class="max-w-5xl mx-auto py-10">
        <h1 class="text-2xl font-bold mb-6">Guide Requests</h1>

        @if (session('success'))
            <div class="mb-6 px-4 py-3 bg-green-100 border text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif

        @foreach($requests as $request)
        @php $trip = $request->trip; @endphp
    
        <div class="bg-white rounded-lg shadow p-5 mb-4">
    
            <p><strong>Status:</strong> {{ $request->status }}</p>
    
            <h2 class="text-xl font-bold mt-2">
                {{ $trip->name ?? '-' }}
            </h2>
    
            <p class="text-gray-600">
                📍 {{ $trip->primaryDestination->city ?? '' }},
                {{ $trip->primaryDestination->country ?? '' }}
            </p>
    
            <p><strong>Duration:</strong> {{ $trip->duration_days }} days</p>
    
            {{-- Dates --}}
            @if($trip->schedules->isNotEmpty())
                <p>
                    <strong>Dates:</strong>
                    {{ optional($trip->schedules->first())->start_date }}
                    →
                    {{ optional($trip->schedules->first())->end_date }}
                </p>
            @endif
    
            <p><strong>Meeting Point:</strong> {{ $trip->meeting_point_address ?? '-' }}</p>
    
            {{-- Activities Preview --}}
            <div class="mt-3">
                <strong>Activities:</strong>
                <ul class="list-disc ml-5">
                    @foreach($trip->days->take(2) as $day)
                        @foreach($day->activities->take(2) as $activity)
                            <li>{{ $activity->activity->name ?? '-' }}</li>
                        @endforeach
                    @endforeach
                </ul>
            </div>
    
            <p class="mt-2 text-sm text-gray-500">
                Expires at: {{ $request->expires_at }}
            </p>
    
            @if($request->status === 'pending')
                <div class="flex gap-3 mt-4">
                    <form method="POST" action="{{ route('guide.requests.accept', $request) }}">
                        @csrf
                        <button class="px-4 py-2 bg-green-600 text-white rounded">Accept</button>
                    </form>
    
                    <form method="POST" action="{{ route('guide.requests.reject', $request) }}">
                        @csrf
                        <button class="px-4 py-2 bg-red-600 text-white rounded">Reject</button>
                    </form>
                </div>
            @endif
    
        </div>
    @endforeach
    </div>
</x-app-layout>