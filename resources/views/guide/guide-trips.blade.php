<x-app-layout>
    @push('styles')
    <link rel="stylesheet" href="{{asset('css/vehicles.css')}}">
    <link rel="stylesheet" href="{{ asset('css/transport.css') }}">
    @endpush

    <div class="main-wrapper p-6 md:p-8"> 

        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-2xl font-bold text-gray-800">
                    Trips for {{ $guide->user->name }}
                </h2>
                <p class="text-gray-500 mt-1">A list of all assigned trips for a this guide.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">

                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Guide Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trip Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Destination</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Start Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Duration</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Participants</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-200">

                        @forelse ($assignments as $assignment)
                            @php $trip = $assignment->trip; @endphp

                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $assignment->guide->user->name ?? 'N/A' }}
                                </td>

                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $trip->name ?? 'N/A' }}
                                </td>

                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $trip->primaryDestination->city ?? '' }},
                                    {{ $trip->primaryDestination->country ?? '' }}
                                </td>

                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ optional($trip->schedules->first())->start_date ?? 'N/A' }}
                                </td>

                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $trip->duration_days ?? 'N/A' }} days
                                </td>

                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $trip->max_participants ?? 'N/A' }}
                                </td>

                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $assignment->status }}
                                </td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                    No assigned trips found.
                                </td>
                            </tr>
                        @endforelse

                    </tbody>

                </table>
            </div>
        </div>
    </div>
</x-app-layout>