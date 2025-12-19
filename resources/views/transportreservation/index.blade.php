<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Transport Reservations
        </h2>
    </x-slot>

    {{-- Search --}}
    <form method="GET" class="mb-4 bg-white p-4 shadow rounded">
        <div class="flex gap-3">
            <input type="search" name="search" value="{{ request('search') }}"
                placeholder="Search by pickup / dropoff /user/Status" class="border p-2 rounded w-full">
            <button class="px-4 py-2 bg-blue-600 text-white rounded">
                Search
            </button>
        </div>
    </form>

    <div class="bg-white p-6 shadow rounded overflow-x-auto">
        <table class="min-w-full text-sm border border-gray-200">
            <thead class="bg-gray-100 text-gray-700 font-semibold">
                <tr>
                    <th class="p-3 border">User</th>
                    <th class="p-3 border">Pickup</th>
                    <th class="p-3 border">Dropoff</th>
                    <th class="p-3 border">Pickup Date</th>
                    <th class="p-3 border">Passengers</th>
                    <th class="p-3 border">Total Price</th>
                    <th class="p-3 border">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reservations as $reservation)
                    <tr class="hover:bg-gray-50">
                        <td class="p-3 border">
                            {{ $reservation->user?->name ?? 'N/A' }}
                        </td>
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
                        <td class="p-3 border">
                            <span class="px-2 py-1 rounded
                                                @if($reservation->status === 'pending') bg-yellow-100 text-yellow-700
                                                @elseif($reservation->status === 'confirmed') bg-green-100 text-green-700
                                                @elseif($reservation->status === 'cancelled') bg-red-100 text-red-700
                                                @endif">
                                {{ ucfirst($reservation->status) }}
                            </span>
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