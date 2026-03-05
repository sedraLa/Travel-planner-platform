<x-app-layout>
    @push('styles')
    <link rel="stylesheet" href="{{asset('css/transportDashboard.css')}}">
    <link rel="stylesheet" href="{{asset('css/vehicles.css')}}">
<link rel="stylesheet" href="{{ asset('css/transport.css') }}">
    @endpush

    <div class="main-container">
       <div class="top-section">
        <div class="head">
            <h1>Transport Dashboard</h1>
        </div>
        <div class="total-cards">
            <div class="total-card">
                <div class="total-info">
                    <h4>System Drivers</h4>
                    <span>{{$systemDrivers}}</span>
                </div>
                <div class="total-icon">
                    <img class="icon" src="{{asset('images/icons/user-group-solid-full (1).svg')}}">
                </div>
            </div>

            <div class="total-card">
                <div class="total-info">
                    <h4>Drivers Requests</h4>
                    <span>{{$driverRequests}}</span>
                </div>
                <div class="total-icon">
                    <img class="icon" src="{{asset('images/icons/icons8-category-50.png')}}">
                </div>
            </div>

            <div class="total-card">
                <div class="total-info">
                    <h4>Vehicles</h4>
                    <span>{{$vehicles}}</span>
                </div>
                <div class="total-icon">
                    <img class="icon" src="{{asset('images/icons/car-side-solid-full.svg')}}">
                </div>
            </div>

            <div class="total-card">
                <div class="total-info">
                    <h4>Pending Reservations</h4>
                    <span>{{$pendingReservations}}</span>
                </div>
                <div class="total-icon">
                    <img class="icon" src="{{asset('images/icons/icons8-pending-50.png')}}">
                </div>
            </div>

            <div class="total-card">
                <div class="total-info">
                    <h4>Completed Reservations</h4>
                    <span>{{$completedReservations}}</span>
                </div>
                <div class="total-icon">
                    <img class="icon" src="{{asset('images/icons/icons8-checkmark-50.png')}}">
                </div>
            </div>
        </div>
    </div>
        <div class="actions-wrapper">
            <!-- Quick Actions -->
            <div class="actions">
                <h4>Quick Actions</h4>
                <a href="{{route('shift-templates.index')}}" class="shifts action-card">
                    <div class="action">
                        <img class="icon" src="{{asset('images/icons/icons8-24-hours-50.png')}}">
                        <h3>Shift Templates</h3>
                    </div>
                    <span class="arrow"> → </span>
                </a>

                <a href="{{route('assignments.index')}}" class="assignments action-card">
                    <div class="action">
                        <img class="icon" src="{{asset('images/icons/calendar-days-solid-full (1).svg')}}">
                        <h3>Assignments</h3>
                    </div>
                    <span class="arrow"> → </span>
                </a>

                <a href="{{ route('drivers.approved.index') }}" class="drivers action-card">
                    <div class="action">
                        <img class="icon" src="{{asset('images/icons/user-group-solid-full (1).svg')}}">
                        <h3>Drivers</h3>
                    </div>
                    <span class="arrow"> → </span>
                </a>

                <a href="{{ route('drivers.request.index') }}" class="drivers action-card">
                    <div class="action">
                        <img class="icon" src="{{asset('images/icons/icons8-category-50.png')}}">
                        <h3>Driver Requests</h3>
                    </div>
                    <span class="arrow"> → </span>
                </a>

                <a href="{{route('admin.vehicles.index')}}" class="vehicles action-card">
                    <div class="action">
                        <img class="icon" src="{{asset('images/icons/car-side-solid-full.svg')}}">
                        <h3>Vehicles</h3>
                    </div>
                    <span class="arrow"> → </span>
                </a>

                <a href="{{route('vehicle.reservations.index')}}" class="vehicles action-card">
                    <div class="action">
                        <img class="icon" src="{{asset('images\icons\icons8-date-50.png')}}">
                        <h3>Reservations</h3>
                    </div>
                    <span class="arrow"> → </span>
                </a>
            </div>

            <!-- Top Ratings Card -->
            <div class="top-ratings-card">
                <h4>Top Ratings</h4>
                <div class="rating-item">
                    <img class="rating-icon" src="{{ asset('images/icons/user-group-solid-full (1).svg') }}">
                    <span class="rating-name">John Doe</span>
                    <span class="rating-stars">★★★★☆</span>
                </div>
                <div class="rating-item">
                    <img class="rating-icon" src="{{ asset('images/icons/car-side-solid-full.svg') }}">
                    <span class="rating-name">Sedan A</span>
                    <span class="rating-stars">★★★★★</span>
                </div>
                <div class="rating-item">
                    <img class="rating-icon" src="{{ asset('images/icons/icons8-checkmark-50.png') }}">
                    <span class="rating-name">Service X</span>
                    <span class="rating-stars">★★★★☆</span>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>