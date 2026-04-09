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
                <p id="rating">⭐ 4.9 rating  {{ $completedTrips }} total trips</p>
                <button onclick="document.getElementById('schedule-modal').classList.remove('hidden')" 
class="schedule-btn">
    Working Schedule
</button>
            </div>
        </div>
    </div>
    <div class ="bookings-summary">
        <!--pending bookings-->
        <div class ="pending">
            <img class="icon" src="{{asset('images/icons/icons8-pending-50.png')}}">
            <div class="count">
            <h3>PENDING</h3>
            <span id="pending-total">{{ $completedTrips}}</span>
            </count>
            </div>
        </div>

        <div class ="completed">
            <img class="icon" src="{{asset('images/icons/icons8-checkmark-50.png')}}">
            <div class="count">
            <h3>COMPLETED</h3>
            <span id="completed-total">{{ $completedTrips }}</span>
            </count>
            </div>
        </div>

        <div class ="canceled">
            <img class="icon" src="{{asset('images/icons/icons8-cancel-50.png')}}">
            <div class="count">
            <h3>CANCELED</h3>
            <span id="canceled-total">{{ $completedTrips }}</span>
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
        <h2 class="vehicle-title">Your Assigned Vehicle</h2>
        <div class="vehicle-info">
           <img class="vehicle-photo" alt="Vehicle Image">

            <div class="vehicle-details">
                <h2 class="vehicle-model">{{  $completedTrips }}</h2>
                <span class="category">{{  $completedTrips }}</span>
                <div class="vehicle-stats">
                    <div class="stat">
                        <img class="stat-icon" src="{{ asset('images/icons/icons8-licence-plate-50.png') }}" alt="Plate Icon">
                        <div>
                            <h5>Plate Number</h5>
                            <h6>{{  $completedTrips }}</h6>
                        </div>
                    </div>
                    <div class="stat">
                        <img class="stat-icon" src="{{ asset('images/icons/icons8-passengers-50.png') }}" alt="Passengers Icon">
                        <div>
                            <h5>Max Passengers</h5>
                            <h6>{{  $completedTrips}}</h6>
                        </div>
                    </div>
                    <div class="stat">
                        <img class="stat-icon" src="{{ asset('images/icons/icons8-price-50.png') }}" alt="Base Price Icon">
                        <div>
                            <h5>Base Price</h5>
                            <h6>{{  $completedTrips }}</h6>
                        </div>
                    </div>
                    <div class="stat">
                        <img class="stat-icon" src="{{ asset('images/icons/icons8-price-50.png') }}" alt="Price per Km Icon">
                        <div>
                            <h5>Price per Km</h5>
                            <h6>{{ $completedTrips }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{--<div id="schedule-modal"
class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">

    <div class="schedule-modal-container">

        <button onclick="document.getElementById('schedule-modal').classList.add('hidden')"
        class="schedule-close-btn">
            &times;
        </button>

        <h2 class="schedule-title">Working Schedule</h2>

        <table class="shift-table">
            <thead>
                <tr>
                    <th>Shift Name</th>
                    <th>Time</th>
                    <th>Days</th>
                </tr>
            </thead>
            <tbody>
               @if($schedules)
                <tr>
                    <td>{{$schedules->name??'no schedula name'}}</td>
                    <td>{{$schedules->start_time ??'no start time'}} -> {{$schedule->end_time??'no end time'}}</td>
                    <td>{{ is_array($schedules->days_of_week) ? implode(', ', $schedules->days_of_week) : ($schedules->days_of_week ?? 'no days') }}</td>
                
                </tr>
                @else
                <tr>
                      <td colspan="3">No schedules found</td>
                </tr>
                  @endif
            </tbody>
        </table>

    </div>
</div>--}}

</x-app-layout>
