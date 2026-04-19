<x-app-layout>
    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/transport.css') }}">
    <link rel="stylesheet" href="{{ asset('css/vehicles.css') }}">
    @endpush
    
     {{--  Filter --}}
     <form method="GET"
     action="{{ route('trip.reservations.index') }}"
     class="mb-6 p-4 rounded-xl shadow flex flex-wrap gap-4 items-end">
{{-- Keyword --}}
<div class="flex-1 min-w-[200px]">
  <label class="text-sm text-gray-600">Search</label>
  <input type="text"
         name="keyword"
         value="{{ request('keyword') }}"
         placeholder="Trip name, package{{ Auth::user()->role === 'admin' ? ', user' : '' }}"
         class="w-full border rounded-lg p-2">
</div>

{{-- Month --}}
<div>
  <label class="text-sm text-gray-600">Month</label>
  <select name="month" class="border rounded-lg p-2">
      <option value="">All</option>
      @for($m = 1; $m <= 12; $m++)
          <option value="{{ $m }}" @selected(request('month') == $m)>
              {{ \Carbon\Carbon::create()->month($m)->format('F') }}
          </option>
      @endfor
  </select>
</div>

{{-- Year --}}
<div>
  <label class="text-sm text-gray-600">Year</label>
  <select name="year" class="border rounded-lg p-2">
      <option value="">All</option>
      @for($y = now()->year; $y >= 2020; $y--)
          <option value="{{ $y }}" @selected(request('year') == $y)>
              {{ $y }}
          </option>
      @endfor
  </select>
</div>


{{-- Actions --}}
<div class="flex gap-2">
  <button class="bg-blue-600 text-white px-4 py-2 rounded-lg">
      Filter
  </button>

  <a href="{{ route('trip.reservations.index') }}"
   class="px-4 py-2 border rounded-lg text-gray-600">
    Reset
</a>
</div>
</form>
    <div class="bg-white p-6 shadow rounded overflow-x-auto">
    
        <h2 class="text-2xl font-bold text-gray-800">Trip Reservations</h2>
    
        @if(Auth::user()->role === 'admin')
            <p class="text-gray-500 mt-1">All trip reservations in your system.</p>
        @else
            <p class="text-gray-500 mt-1">Your trip reservations.</p>
        @endif
    
        <table class="min-w-full text-sm border border-gray-200 mt-4">
            <thead class="bg-gray-100 text-gray-700 font-semibold">
                <tr>
                    @if(Auth::user()->role === 'admin')
                        <th class="p-3 border">User</th>
                    @endif
                    <th class="p-3 border">Trip</th>
                    <th class="p-3 border">Package</th>
                    <th class="p-3 border">Schedule</th>
                    <th class="p-3 border">People</th>
                    <th class="p-3 border">Total Price</th>
                    <th class="p-3 border">Status</th>
                    @if(Auth::user()->role !== 'admin')
                        <th class="p-3 border">Trip Review</th>
                        <th class="p-3 border">Guide Review</th>
                    @endif
                </tr>
            </thead>
    
            <tbody>
                @forelse($reservations as $r)
                    <tr class="hover:bg-gray-50">
    
                        @if(Auth::user()->role === 'admin')
                        <td class="p-3 border">
                            {{ $r->user?->name ?? 'N/A' }}
                        </td>
                        @endif
    
                        <td class="p-3 border">
                            {{ $r->trip?->name }}
                        </td>
    
                        <td class="p-3 border">
                            {{ $r->package?->name }}
                        </td>
    
                        <td class="p-3 border">
                            {{ \Carbon\Carbon::parse($r->schedule?->start_date)->format('d M Y') }}
                        </td>
    
                        <td class="p-3 border text-center">
                            {{ $r->people_count }}
                        </td>
    
                        <td class="p-3 border text-green-600 font-semibold">
                            ${{ number_format($r->total_price, 2) }}
                        </td>
    
                        <td class="p-3 border">
                            <span class="px-2 py-1 rounded text-xs
                                {{ $r->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                {{ $r->status === 'paid' ? 'bg-green-100 text-green-700' : '' }}">
                                {{ ucfirst($r->status) }}
                            </span>
                        </td>

                        @if(Auth::user()->role !== 'admin')
                            <td class="p-3 border">
                                @if(in_array((int) $r->id, $reviewedReservationIds ?? [], true))
                                    <button type="button" disabled class="px-3 py-1 rounded bg-gray-300 text-gray-700 cursor-not-allowed">
                                        Rated
                                    </button>
                                @else
                                    <a href="{{ route('reviews.create', ['type' => 'trip', 'id' => $r->trip_id, 'reservation_id' => $r->id]) }}"
                                       class="px-3 py-1 rounded bg-indigo-600 text-white hover:bg-indigo-700">
                                        Rate Now
                                    </a>
                                @endif
                            </td>

                            <td class="p-3 border">
                                @php($guideId = $r->trip?->assigned_guide_id)
                                @if(!$guideId)
                                    <span class="text-gray-500">N/A</span>
                                @elseif(in_array((int) $r->id, $reviewedReservationIds ?? [], true))
                                    <button type="button" disabled class="px-3 py-1 rounded bg-gray-300 text-gray-700 cursor-not-allowed">
                                        Rated
                                    </button>
                                @else
                                    <a href="{{ route('reviews.create', ['type' => 'guide', 'id' => $guideId, 'reservation_id' => $r->id]) }}"
                                       class="px-3 py-1 rounded bg-indigo-600 text-white hover:bg-indigo-700">
                                        Rate Now
                                    </a>
                                @endif
                            </td>
                        @endif
    
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ Auth::user()->role === 'admin' ? 7 : 9 }}" class="p-4 text-center text-gray-500">
                            No trip reservations found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    
    </div>
    </x-app-layout>
