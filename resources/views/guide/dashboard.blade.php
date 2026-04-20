@push('styles')
<link rel="stylesheet" href="{{asset('css/vehicles.css')}}">
<link rel="stylesheet" href="{{ asset('css/transport.css') }}">
<link rel="stylesheet" href="{{ asset('css/driver_dash.css') }}">
@endpush
<x-app-layout>
    <div class="main">
    <div class="driver_profile">
        <div class="personal_info">
            <img class="personal-photo" src="{{ $guide&& $guide->personal_image ? asset('storage/' . $guide->personal_image) : asset('images/ian-dooley-d1UPkiFd04A-unsplash.jpg') }}">
            <div class="personal-details">
                <span>Good day</span>
                <h2>Welcome back, {{ $guide?->user?->name ?? auth()->user()->name }} 👋</h2>
                <p id="rating">⭐ 4.9 rating  {{ $assignedTrips }} Assigned trips</p>
               <button onclick="openSchedule({{ $guide->id }})"
        class="schedule-btn">
    Working Schedule
</button>
            </div>
        </div>
    </div>
    <div class ="bookings-summary">
        <!--pending booking requests-->
        <div class ="pending">
            <img class="icon" src="{{asset('images/icons/icons8-pending-50.png')}}">
            <div class="count">
            <h3>REQUESTS</h3>
            <span id="pending-total">{{ $pendingRequests}}</span>
            </count>
            </div>
        </div>

        <div class ="completed">
            <img class="icon" src="{{asset('images/icons/icons8-checkmark-50.png')}}">
            <div class="count">
            <h3>TRIPS</h3>
            <span id="completed-total">{{ $assignedTrips }}</span>
            </count>
            </div>
        </div>

        <div class ="canceled">
            <img class="icon" src="{{asset('images/icons/icons8-cancel-50.png')}}">
            <div class="count">
            <h3>CANCELED</h3>
            <span id="canceled-total">{{ $rejectedTrips }}</span>
            </count>
            </div>
        </div>

        <div class ="earning">
            <img class="icon" src="{{asset('images/icons/icons8-earning-50.png')}}">
            <div class="count">
            <h3>EARNINGS</h3>
            <span id="earning-total">2 <!--number of pending booings--> </span>
            {{--<span id="earning-total">{{ $completedTrips}}</span>--}}
            </count>
            </div>
        </div>
    </div>

    <div class="assigned-vehicle">
        <h2 class="vehicle-title">Your Latest Assigned Trip</h2>
    
        <div class="vehicle-info">
            <img class="vehicle-photo"
                 src="{{ $trip?->images->first() ? asset('storage/' . $trip->images->first()->path) : asset('images/default-trip.jpg') }}"
                 alt="Trip Image">
    
            <div class="vehicle-details">
    
                <h2 class="vehicle-model">
                    {{ $trip->name ?? 'No assigned trip yet' }}
                </h2>
    
                <span class="category">
                  To:  {{ $trip?->primaryDestination?->name ?? 'No destination' }}
                </span>
    
                <div class="vehicle-stats">
    
                    <div class="stat">
                        <img class="stat-icon" src="{{ asset('images/icons/calendar-days-solid-full (1).svg') }}">
                        <div>
                            <h5>Start Date</h5>
                            <h6>
                                {{ optional($trip?->schedules->first())->start_date ?? 'N/A' }}
                            </h6>
                        </div>
                    </div>
    
                    <div class="stat">
                        <img class="stat-icon" src="{{ asset('images/icons/user-group-solid-full.svg') }}">
                        <div>
                            <h5> Participants</h5>
                            <h6>{{ $trip->max_participants ?? 'N/A' }}</h6>
                        </div>
                    </div>
    
                    <div class="stat">
                        <img class="stat-icon" src="{{ asset('images/icons/icons8-24-hours-50.png') }}">
                        <div>
                            <h5>Duration</h5>
                            <h6>{{ $trip->duration_days ?? 'N/A' }} days</h6>
                        </div>
                    </div>
    
                    <div class="stat">
                        <img class="stat-icon" src="{{ asset('images/icons/icons8-tour-50.png') }}">
                        <div>
                            <h5>Category</h5>
                            <h6>{{ $trip->category ?? 'N/A' }}</h6>
                        </div>
                    </div>
    
                </div>
            </div>
        </div>
    </div>
</div>

<div id="schedule-modal"
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">

    <div class="bg-white p-6 rounded-lg w-[600px] relative">

        <button onclick="document.getElementById('schedule-modal').classList.add('hidden')"
                class="absolute top-2 right-3 text-2xl">
            &times;
        </button>

        <h2 class="text-xl font-bold mb-4">Working Days</h2>

        <table class="w-full border">
            <thead>
                <tr>
                    <th class="border p-2">Date</th>
                </tr>
            </thead>

            <tbody id="schedule-body">
                <tr>
                    <td class="p-2">Loading...</td>
                </tr>
            </tbody>
        </table>

    </div>
</div>


<script>
document.addEventListener("DOMContentLoaded", function () {

    window.openSchedule = function (guideId) {

        console.log("Guide ID:", guideId);

        fetch(`/guide/${guideId}/availability`)
            .then(response => {
                if (!response.ok) {
                    throw new Error("HTTP error " + response.status);
                }
                return response.json();
            })
            .then(data => {

                console.log("Response:", data);

                let html = '';

                if (!data.dates || data.dates.length === 0) {
                    html = `
                        <tr>
                            <td class="p-2 text-center text-gray-500">
                                No working days assigned
                            </td>
                        </tr>
                    `;
                } else {
                    data.dates.forEach(date => {
                        html += `
                            <tr>
                                <td class="p-2">${date}</td>
                            </tr>
                        `;
                    });
                }

                document.getElementById('schedule-body').innerHTML = html;

                document.getElementById('schedule-modal')
                    .classList.remove('hidden');
            })
            .catch(error => {

                console.error("Fetch error:", error);

                document.getElementById('schedule-body').innerHTML = `
                    <tr>
                        <td class="p-2 text-red-500 text-center">
                            Failed to load schedule
                        </td>
                    </tr>
                `;

                document.getElementById('schedule-modal')
                    .classList.remove('hidden');
            });
    };

});
</script>

</x-app-layout>
