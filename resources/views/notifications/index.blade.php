<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/vehicles.css') }}">
        <link rel="stylesheet" href="{{ asset('css/transport.css') }}">
    @endpush

    <div class="main-wrapper p-6 md:p-8">
        <h2 class="text-2xl font-bold mb-6">All Notifications</h2>

        @forelse($notifications as $notification)

            @php
                $data = $notification->data ?? [];

                $isTripNotification = ($data['type'] ?? null) === 'trip_reservation';
                $isTransportNotification = ($data['type'] ?? null) === 'transport_reservation';
                $isReviewNotification = ($data['type'] ?? null) === 'review_request';
                $isAssignmentNotification = !empty($data['assignment_id']);
            @endphp

            <div class="bg-white rounded-xl shadow-lg p-6 mb-4 {{ $notification->read_at ? '' : 'bg-gray-100' }}">

                <div class="flex justify-between items-start mb-3 gap-3">
                    <div>
                        <h3 class="font-semibold text-lg">
                            {{ $data['message'] ?? '-' }}
                        </h3>

                        <span class="inline-block mt-1 text-xs font-semibold px-2 py-1 rounded-full
                            @if($isAssignmentNotification)
                                bg-indigo-300 text-indigo-900
                            @elseif($isTripNotification)
                                bg-emerald-100 text-emerald-900
                            @elseif($isTransportNotification)
                                bg-blue-100 text-blue-900
                            @else
                                bg-gray-100 text-gray-800
                            @endif
                        ">
                            @if($isAssignmentNotification)
                                Assignment Notification
                            @elseif($isTripNotification)
                                Trip Booking
                            @elseif($isTransportNotification)
                                Transport Booking
                            @elseif($isReviewNotification)
                                Review Notification
                            @else
                                General Notification
                            @endif
                        </span>
                    </div>

                    <span class="text-sm {{ $notification->read_at ? 'text-green-600' : 'text-red-600' }}">
                        {{ $notification->read_at ? 'Read' : 'Unread' }}
                    </span>
                </div>

                {{-- ASSIGNMENT --}}
                @if ($isAssignmentNotification)

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p><strong>Vehicle:</strong> {{ $data['vehicle'] ?? '-' }}</p>
                            <p><strong>Plate Number:</strong> {{ $data['plate_number'] ?? '-' }}</p>
                            <p><strong>Shift:</strong> {{ $data['shift'] ?? '-' }}</p>
                        </div>
                        <div>
                            <p><strong>Shift Start:</strong> {{ $data['shift_start'] ?? '-' }}</p>
                            <p><strong>Shift End:</strong> {{ $data['shift_end'] ?? '-' }}</p>
                        </div>
                    </div>

                {{-- TRIP RESERVATION --}}
                @elseif ($isTripNotification)

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p><strong>Trip:</strong> {{ $data['trip_name'] ?? '-' }}</p>
                            <p><strong>Reservation ID:</strong> {{ $data['reservation_id'] ?? '-' }}</p>
                        </div>
                    </div>

                    {{-- REVIEW RESERVATION --}}
                    @elseif ($isReviewNotification)
                    <div class="flex items-center justify-between">
                        <p class="text-gray-700">
                            Please rate your experience for:
                            <span class="font-semibold text-indigo-600">
                                {{ $data['review_name'] ?? 'this item' }}
                            </span>
                        </p>

                        @if(($data['is_reviewed'] ?? false) === true)
                            <button
                                type="button"
                                disabled
                                class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg cursor-not-allowed"
                            >
                                Rated
                            </button>
                        @else
                        @if(isset($data['reservation_id']))
                        <a href="{{ route('reviews.create', [
                            'type' => $data['review_type'],
                            'id' => $data['review_id'],
                            'reservation_id' => $data['reservation_id']
                        ]) }}">
                            Rate Now
                        </a>
                    @endif
                        @endif
                    </div>


                {{-- TRANSPORT RESERVATION --}}
                @elseif ($isTransportNotification)

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p><strong>Pickup:</strong> {{ $data['pickup'] ?? '-' }}</p>
                            <p><strong>Dropoff:</strong> {{ $data['dropoff'] ?? '-' }}</p>
                            <p><strong>Date:</strong> {{ $data['pickup_datetime'] ?? '-' }}</p>
                        </div>

                        <div>
                            <p><strong>Name:</strong> {{ $data['full_name'] ?? '-' }}</p>

                            <p><strong>Phone:</strong>
                                @if(!empty($data['phone_number']))
                                    <a href="tel:{{ $data['phone_number'] }}" class="text-blue-600 hover:underline">
                                        {{ $data['phone_number'] }}
                                    </a>
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                    </div>

                @else
                    <p class="text-sm text-gray-700">
                        No additional details for this notification type.
                    </p>
                @endif

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
