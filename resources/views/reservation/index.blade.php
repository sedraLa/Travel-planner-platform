<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Reservations') }}
        </h2>
    </x-slot>
    {{-- 🔎 Admin-only Search (فقط بحث) --}}
    @if(Auth::check() && Auth::user()->role === 'admin')
        <form method="GET" action="{{ route('reservations.index') }}" class="mb-4 bg-white p-4 shadow rounded">
            <div class="flex gap-3">
                <input type="search" name="search" value="{{ request('search') }}"
                    placeholder="Search by Hotel / City / Country / User" class="border p-2 rounded w-full" />
                <button class="px-4 py-2 bg-blue-600 text-white rounded">Search</button>
            </div>
        </form>
    @endif
    {{-- /Admin-only Search --}}

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if($reservations->isEmpty())
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
                                @if(Auth::user()->role === 'admin')
                                    <th class="p-3 border text-center">Actions</th>
                                @endif
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
                                        <span
                                            class="px-2 py-1 rounded
                                                                                                                                                                                                            @if($reservation->reservation_status == 'pending') bg-yellow-100 text-yellow-700
                                                                                                                                                                                                            @elseif($reservation->reservation_status == 'confirmed') bg-green-100 text-green-700
                                                                                                                                                                                                            @elseif($reservation->reservation_status == 'cancelled') bg-red-100 text-red-700
                                                                                                                                                                                                            @endif">
                                            {{ ucfirst($reservation->reservation_status) }}
                                        </span>
                                    </td>
                                    @if(Auth::user()->role === 'admin')
                                        <td class="p-3 border text-center">
                                            <a href="#" class="text-blue-500 hover:underline text-sm mr-2">View</a>
                                            <a href="#" class="text-green-500 hover:underline text-sm mr-2">Edit</a>
                                            <a href="#" class="text-red-500 hover:underline text-sm">Delete</a>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>