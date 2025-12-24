<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/vehicles.css') }}">
        <link rel="stylesheet" href="{{ asset('css/transport.css') }}">
    @endpush

    <div class="main-wrapper p-6 md:p-8">
        <h2 class="text-2xl font-bold mb-4">All Notifications</h2>

        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">

                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                Message
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                Pickup
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                Dropoff
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                Name
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                Phone
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                Pickup Date
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                Status
                            </th>
                        </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($notifications as $notification)
                            <tr class="{{ $notification->read_at ? '' : 'bg-gray-100' }}">

                                <td class="px-6 py-4">
                                    {{ $notification->data['message'] ?? '-' }}
                                </td>

                                <td class="px-6 py-4">
                                    {{ $notification->data['pickup'] ?? '-' }}
                                </td>

                                <td class="px-6 py-4">
                                    {{ $notification->data['dropoff'] ?? '-' }}
                                </td>

                                <td class="px-6 py-4 font-medium text-gray-800">
                                    {{ $notification->data['full_name'] ?? '-' }}
                                </td>

                                <td class="px-6 py-4">
                                    @if(!empty($notification->data['phone_number']))
                                        <a href="tel:{{ $notification->data['phone_number'] }}"
                                           class="text-blue-600 hover:underline">
                                            {{ $notification->data['phone_number'] }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>

                                <td class="px-6 py-4">
                                    {{ $notification->data['pickup_datetime'] ?? '-' }}
                                </td>

                                <td class="px-6 py-4">
                                    @if($notification->read_at)
                                        <span class="text-green-600 font-medium">Read</span>
                                    @else
                                        <span class="text-red-600 font-medium">Unread</span>
                                    @endif
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    No notifications found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>

                <div class="p-4">
                    {{ $notifications->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
