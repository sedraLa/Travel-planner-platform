<x-app-layout>
    @push('styles')
    <link rel="stylesheet" href="{{asset('css/vehicles.css')}}">
    <link rel="stylesheet" href="{{ asset('css/transport.css') }}">
    @endpush

    <div class="max-w-5xl mx-auto py-10">
        <h1 class="text-2xl font-bold mb-6">Assigned Trips</h1>

        @forelse($assignments as $assignment)
            @php $trip = $assignment->trip; @endphp

            <div class="bg-white rounded-lg shadow p-5 mb-4">

                <p><strong>Status:</strong> {{ $assignment->status }}</p>

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

                {{-- Activities --}}
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
                    Assigned at: {{ $assignment->created_at }}
                </p>

                <div class="flex justify-end mt-4">
                    <button
                        class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition duration-200 text-sm font-semibold shadow">
                        I want to withdraw from supervising this trip
                    </button>
                </div>
                        
                                
                 
                         @php
                       $reservation = $reservations[$trip->id] ?? null;
                           $isDisabled = !$reservation ||  $reservation->guide_paid_at ||
                          now()->lt(optional($reservation->schedule)->end_date);
                             @endphp

                   <div class="flex justify-end mt-4">
                      <form method="POST" action="{{ $reservation ? route('guide.trips.complete', $reservation->id) : '#' }}">
                              @csrf

                              <buttontype="submit" class="px-4 py-2 rounded text-white {{ $isDisabled ? 'bg-gray-400 cursor-not-allowed' : 'bg-green-600 hover:bg-green-700' }}" {{ $isDisabled ? 'disabled' : '' }}>
                                     Trip Done
                               </button>
                    </form>
                </div>

            </div>

        @empty
            <div class="bg-white rounded-lg shadow p-5">
                <p>No assigned trips yet.</p>
            </div>
        @endforelse
    </div>
</x-app-layout>