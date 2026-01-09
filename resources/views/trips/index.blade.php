<x-app-layout>
    @push('styles')
    <link rel="stylesheet" href="{{asset('css/vehicles.css')}}">
    <link rel="stylesheet" href="{{ asset('css/transport.css') }}">
    @endpush

    <style>
        .container {
            max-width:1400px;
        }
        </style>

<div class="container mx-auto py-8">
    <h1 class="text-3xl font-bold mb-6">My Trips</h1>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-4 mb-4 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($trips as $trip)
            <div class="bg-white shadow-md rounded p-4 border border-gray-200">
                <h2 class="text-xl font-semibold">{{ $trip->name }}</h2>
                @if($trip->is_ai)
                    <span class="inline-block bg-blue-200 text-blue-800 px-2 py-1 text-xs rounded mt-1">AI Trip</span>
                @endif
                <p class="mt-2 text-gray-600">{{ Str::limit($trip->description, 100) }}</p>
                <p class="mt-2 text-gray-500 text-sm">
                    Dates: {{ $trip->start_date }} - {{ $trip->end_date }}
                </p>
                <p class="text-gray-500 text-sm">Travelers: {{ $trip->travelers_number }}</p>


                @if($trip->is_ai)
                <div class="mt-4 flex justify-between">
                    <a href="{{ route('ai.show', $trip->id) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">View Details</a>
                    @else
                    <div class="mt-4 flex justify-between">
                        <a href="{{ route('manual.show', $trip->id) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">View Details</a>
                        @endif
                    
                    <form action="{{ route('trip.destroy', $trip->id) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">Delete</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
</div>

</x-app-layout>