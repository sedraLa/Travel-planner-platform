<x-app-layout>
    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/transport.css') }}">
    <link rel="stylesheet" href="{{ asset('css/vehicles.css') }}">
@endpush
   

    {{--  Filter --}}
<form method="GET"
class="mb-6  p-4 rounded-xl shadow flex flex-wrap gap-4 items-end">

{{-- Keyword --}}
<div class="flex-1 min-w-[200px]">
  <label class="text-sm text-gray-600">Search</label>
  <input type="text"
         name="keyword"
         value="{{ request('keyword') }}"
         placeholder="Pickup, Dropoff{{ Auth::user()->role === 'admin' ? ', user' : '' }}"
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

  <a href="{{ route('transport.reservations.index') }}"
     class="px-4 py-2 border rounded-lg text-gray-600">
      Reset
  </a>
</div>
</form>


    <div class="bg-white p-6 shadow rounded overflow-x-auto">
        <h2 class="text-2xl font-bold text-gray-800">Transport Reservations</h2>
                    @if(Auth::user()->role === 'admin')
                    <p class="text-gray-500 mt-1">A list of all the transport reservations in your system.</p>
                    @else 
                    <p class="text-gray-500 mt-1">A list of all your transport reservations.</p>
                    @endif
        <table class="min-w-full text-sm border border-gray-200">
            <thead class="bg-gray-100 text-gray-700 font-semibold">
                <tr>
                    @if(Auth::user()->role === 'admin')
                    <th class="p-3 border">User</th>
                    @endif
                    <th class="p-3 border">Pickup</th>
                    <th class="p-3 border">Dropoff</th>
                    <th class="p-3 border">Pickup Date</th>
                    <th class="p-3 border">Passengers</th>
                    <th class="p-3 border">Total Price</th>
             
                </tr>
            </thead>
            <tbody>
                @forelse($reservations as $reservation)
                    <tr class="hover:bg-gray-50">
                        @if(Auth::user()->role === 'admin')
                        <td class="p-3 border">
                            {{ $reservation->user?->name ?? 'N/A' }}
                        </td>
                        @endif
                        <td class="p-3 border">
                            {{ $reservation->pickup_location }}
                        </td>
                        <td class="p-3 border">
                            {{ $reservation->dropoff_location }}
                        </td>
                        <td class="p-3 border">
                            {{ $reservation->pickup_datetime }}
                        </td>
                        <td class="p-3 border text-center">
                            {{ $reservation->passengers }}
                        </td>
                        <td class="p-3 border text-green-600 font-semibold">
                            ${{ number_format($reservation->total_price, 2) }}
                        </td>
                        
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-4 text-center text-gray-500">
                            No transport reservations found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-app-layout>
