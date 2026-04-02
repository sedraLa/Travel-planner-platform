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
    
 {{-- Search Form  --}}
 <form method="GET" action="{{ route('trips.index') }}" class="flex flex-wrap gap-4 items-end mb-6">
    {{-- Keyword --}}
    <div class="flex-1 min-w-[200px]">
        <label class="text-sm text-gray-600">Search</label>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search a trip by its name" class="w-full border rounded-lg p-2">
    </div>

    

    {{--Category --}} {{--needs editing--}}
    <div>
        <label class="text-sm text-gray-600">Category</label>
        <select name="license_category" class="border rounded-lg p-2" style="margin-bottom:0">
            <option value="">All</option>
            <option value="A" @selected(request('license_category')=='A')>A</option>
            <option value="B" @selected(request('license_category')=='B')>B</option>
        </select>
    </div>


    
    {{-- Status --}}
    <div>
        <label class="text-sm text-gray-600">Status</label>
        <select name="status" class="border rounded-lg p-2" style="margin-bottom:0">
            <option value="">All</option>
            <option value="draft" @selected(request('status')=='draft')>Draft</option>
        </select>
    </div>

    {{-- Destination --}} 
    <div>
        <label class="text-sm text-gray-600">Destination</label>
        <input type="text" name="destination" value="{{ request('destination') }}" placeholder="destination" class="border rounded-lg p-2">
    </div>

    {{-- Actions --}}
    <div class="flex gap-2">
        <button class="bg-blue-600 text-white px-4 py-2 rounded-lg">Filter</button>
        <a href="{{ route('trips.index') }}" class="px-4 py-2 border rounded-lg text-gray-600">Reset</a>
    </div>
</form>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        
        
        @foreach($trips as $trip)
            <div class="bg-white shadow-md rounded p-4 border border-gray-200">
                <h2 class="text-xl font-semibold">{{ $trip->name }}</h2>
                @if($trip->is_ai_generated)
                    <span class="inline-block bg-blue-200 text-blue-800 px-2 py-1 text-xs rounded mt-1">AI Trip</span>
                @endif
                <p class="mt-2 text-gray-600">{{ \Illuminate\Support\Str::limit($trip->ai_prompt, 100) }}</p>
                <p class="mt-2 text-gray-500 text-sm">
                    Destination: {{ $trip->destination?->name ?? '-' }}
                </p>
                <p class="text-gray-500 text-sm">Duration: {{ $trip->duration_days }} day(s)</p>
                <p class="text-gray-500 text-sm">Travelers: {{ $trip->max_participants ?? '-' }}</p>


                @if($trip->is_ai_generated)
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
