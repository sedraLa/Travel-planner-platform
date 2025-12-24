<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/vehicles.css') }}">
        <link rel="stylesheet" href="{{ asset('css/transport.css') }}">
    @endpush

    <div class="main-wrapper p-6 md:p-8">
        <h2 class="text-2xl font-bold mb-6">All Notifications</h2>

        @forelse($notifications as $notification)
            <div class="bg-white rounded-xl shadow-lg p-6 mb-4 {{ $notification->read_at ? '' : 'bg-gray-100' }}">
                <div class="flex justify-between items-center mb-2">
                    <h3 class="font-semibold text-lg">{{ $notification->data['message'] ?? '-' }}</h3>
                    <span class="text-sm {{ $notification->read_at ? 'text-green-600' : 'text-red-600' }}">
                        {{ $notification->read_at ? 'Read' : 'Unread' }}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p><strong>Pickup:</strong> {{ $notification->data['pickup'] ?? '-' }}</p>
                        <p><strong>Dropoff:</strong> {{ $notification->data['dropoff'] ?? '-' }}</p>
                        <p><strong>Pickup Date:</strong> {{ $notification->data['pickup_datetime'] ?? '-' }}</p>
                    </div>
                    <div>
                        <p><strong>Name:</strong> {{ $notification->data['full_name'] ?? '-' }}</p>
                        <p><strong>Phone:</strong>
                            @if(!empty($notification->data['phone_number']))
                                <a href="tel:{{ $notification->data['phone_number'] }}" class="text-blue-600 hover:underline">
                                    {{ $notification->data['phone_number'] }}
                                </a>
                            @else
                                -
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl shadow-lg p-6 text-center text-gray-500">
                No notifications found.
            </div>
        @endforelse

        <div class="mt-4">
            {{ $notifications->links() }}
        </div>
    </div>
</x-app-layout>
