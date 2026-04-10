@push('styles')
<link rel="stylesheet" href="{{asset('css/vehicles.css')}}">
<link rel="stylesheet" href="{{ asset('css/transport.css') }}">
@endpush

<x-app-layout>
    <div class="main-wrapper p-6 md:p-8">
        @if (session('success'))
            <div class="mb-6 px-4 py-3 bg-green-100 border border-green-200 text-green-800 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-2xl font-bold text-gray-800">My Booking Requests</h2>
                <p class="text-gray-500 mt-1">Incoming trip requests assigned to you.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trip</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Destination</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dates</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Activities</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expires</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($requests as $request)
                            @php
                                $trip = $request->trip;
                                $activityNames = $trip?->days?->flatMap(fn($day) => $day->activities?->pluck('activity.name') ?? collect())->filter()->unique()->values()->all() ?? [];
                            @endphp
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $trip?->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $trip?->primaryDestination?->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $trip?->schedules?->min('start_date') ?? 'N/A' }} → {{ $trip?->schedules?->max('end_date') ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ !empty($activityNames) ? implode(', ', $activityNames) : 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm font-semibold capitalize">{{ $request->status }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ optional($request->expires_at)->format('Y-m-d H:i') ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm">
                                    @if($request->status === 'pending')
                                        <div class="flex items-center gap-2">
                                            <form method="POST" action="{{ route('guide.booking-requests.accept', $request) }}">
                                                @csrf
                                                <button class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600">Accept</button>
                                            </form>
                                            <form method="POST" action="{{ route('guide.booking-requests.reject', $request) }}">
                                                @csrf
                                                <button class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">Reject</button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-gray-500">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">No booking requests found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
