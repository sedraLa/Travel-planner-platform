<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/transport.css') }}">
        <link rel="stylesheet" href="{{ asset('css/vehicles.css') }}">
    @endpush
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @if(Auth::user()->role === 'admin')
            {{ __('System Reservations') }}
            @else
            {{ __('My Reservations') }}
            @endif
        </h2>
    </x-slot>

    {{-- 🔎 Search --}}



    <form method="GET"
    class="mb-6 bg-white p-4 rounded-xl shadow flex flex-wrap gap-4 items-end">

  {{-- Keyword --}}
  <div class="flex-1 min-w-[200px]">
      <label class="text-sm text-gray-600">Search</label>
      <input type="text"
             name="keyword"
             value="{{ request('keyword') }}"
             placeholder="Hotel, destination{{ Auth::user()->role === 'admin' ? ', user' : '' }}"
             class="w-full border rounded-lg p-2">
  </div>

  {{-- Month --}}
  <div>
      <label class="text-sm text-gray-600">Month</label>
      <select name="month" class="border rounded-lg p-2">
          <option value="">All</option>
          @for($m=1;$m<=12;$m++)
              <option value="{{ $m }}" @selected(request('month')==$m)>
                  {{ Carbon\Carbon::create()->month($m)->format('F') }}
              </option>
          @endfor
      </select>
  </div>

  {{-- Year --}}
  <div>
      <label class="text-sm text-gray-600">Year</label>
      <select name="year" class="border rounded-lg p-2">
          <option value="">All</option>
          @for($y=now()->year; $y>=2020; $y--)
              <option value="{{ $y }}" @selected(request('year')==$y)>
                  {{ $y }}
              </option>
          @endfor
      </select>
  </div>

  {{-- Status --}}
  <div>
      <label class="text-sm text-gray-600">Status</label>
      <select name="reservation_status" class="border rounded-lg p-2">
            <option value="">All</option>
            <option value="pending" @selected(request('reservation_status')=='pending')>Pending</option>
            <option value="confirmed" @selected(request('reservation_status')=='confirmed')>Confirmed</option>
        </select>
  </div>

  {{-- Actions --}}
  <div class="flex gap-2">
      <button class="bg-blue-600 text-white px-4 py-2 rounded-lg">
          Filter
      </button>

      <a href="{{ route('reservations.index') }}"
         class="px-4 py-2 border rounded-lg text-gray-600">
          Reset
      </a>
  </div>
</form>



    @if(Auth::check() && Auth::user()->role === 'admin')
        {{-- الأدمن: بحث شامل --}}
        <form method="GET" action="{{ route('reservations.index') }}" class="mb-4 bg-white p-4 shadow rounded">
                <div class="bg-white p-6 shadow rounded text-gray-600">
                    No reservations found.
                </div>
            @else
                <div class="overflow-x-auto bg-white p-6 shadow rounded">
                    <table class="min-w-full text-sm text-left border border-gray-200">
                        <thead class="bg-gray-100 font-semibold text-gray-700">
                            <tr>
                                @if(Auth::user()->role === 'admin')
                                    <th class="p-3 border">User</th>
                                @endif
                                <th class="p-3 border">Hotel</th>
                                <th class="p-3 border">Check-in</th>
                                <th class="p-3 border">Check-out</th>
                                <th class="p-3 border">Rooms</th>
                                <th class="p-3 border">Guests</th>
                                <th class="p-3 border">Total Price</th>
                                <th class="p-3 border">Status</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-800">
                            @foreach($reservations as $reservation)
                                <tr class="hover:bg-gray-50">
                                    @if(Auth::user()->role === 'admin')
                                        <td class="p-3 border">
                                            {{ $reservation->user?->full_name ?? 'N/A' }}
                                        </td>
                                    @endif

                                    <td class="p-3 border">{{ $reservation->hotel->name ?? 'N/A' }}</td>
                                    <td class="p-3 border">{{ $reservation->check_in_date }}</td>
                                    <td class="p-3 border">{{ $reservation->check_out_date }}</td>

                                    <td class="p-3 border text-center">{{ $reservation->rooms_count }}</td>
                                    <td class="p-3 border text-center">{{ $reservation->guest_count }}</td>

                                    <td class="p-3 border font-semibold text-green-600">
                                        ${{ number_format($reservation->total_price, 2) }}
                                    </td>
                                    <td class="p-3 border">
                                        <span class="px-2 py-1 rounded
                                                                    @if($reservation->reservation_status == 'pending') bg-yellow-100 text-yellow-700
                                                                    @elseif($reservation->reservation_status == 'confirmed') bg-green-100 text-green-700
                                                                    @elseif($reservation->reservation_status == 'cancelled') bg-red-100 text-red-700
                                                                    @endif">
                                            {{ ucfirst($reservation->reservation_status) }}
                                        </span>
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
