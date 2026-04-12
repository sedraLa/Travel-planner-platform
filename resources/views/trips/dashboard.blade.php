<x-app-layout>
    @push('styles')
    <link rel="stylesheet" href="{{asset('css/transportDashboard.css')}}">
    <link rel="stylesheet" href="{{asset('css/vehicles.css')}}">
<link rel="stylesheet" href="{{ asset('css/transport.css') }}">
    @endpush

    <div class="main-container">
       <div class="top-section">
        <div class="head">
            <h1>Trips Dashboard</h1>
        </div>
        <div class="total-cards">
            <div class="total-card">
                <div class="total-info">
                    <h4>System Guides</h4>
                    <span>{{$systemGuides}}</span>
                </div>
                <div class="total-icon">
                    <img class="icon" src="{{asset('images/icons/user-group-solid-full (1).svg')}}">
                </div>
            </div>

            <div class="total-card">
                <div class="total-info">
                    <h4>Guides Requests</h4>
                    <span>{{$guidesRequests}}</span>
                </div>
                <div class="total-icon">
                    <img class="icon" src="{{asset('images/icons/icons8-category-50.png')}}">
                </div>
            </div>

            <div class="total-card">
                <div class="total-info">
                    <h4>Activities</h4>
                    <span>{{$activities}}</span>
                </div>
                <div class="total-icon">
                    <img class="icon" src="{{asset('images/icons/icons8-activities-50.png')}}">
                </div>
            </div>

            <div class="total-card">
                <div class="total-info">
                    <h4>Published Trips</h4>
                    <span>{{$publishedTrips}}</span>
                </div>
                <div class="total-icon">
                    <img class="icon" src="{{asset('images/icons/icons8-tour-50.png')}}">
                </div>
            </div>

            <div class="total-card">
                <div class="total-info">
                    <h4>Draft Trips</h4>
                    <span>{{$draftTrips}}</span>
                </div>
                <div class="total-icon">
                    <img class="icon" src="{{asset('images/icons/icons8-pending-50.png')}}">
                </div>
            </div>

            <div class="total-card">
                <div class="total-info">
                    <h4>Completed Reservations</h4>
                    <span>6</span>
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
                <a href="{{ route('guides.index') }}" class="drivers action-card">
                    <div class="action">
                        <img class="icon" src="{{asset('images/icons/user-group-solid-full (1).svg')}}">
                        <h3>Guides</h3>
                    </div>
                    <span class="arrow"> → </span>
                </a>

                <a href="{{ route('guide-applications.index') }}" class="drivers action-card">
                    <div class="action">
                        <img class="icon" src="{{asset('images/icons/icons8-category-50.png')}}">
                        <h3>Guides Requests</h3>
                    </div>
                    <span class="arrow"> → </span>
                </a>

                <a href="{{ route('trips.index') }}" class="vehicles action-card">
                    <div class="action">
                        <img class="icon" src="{{asset('images/icons/icons8-tour-50.png')}}">
                        <h3>Trips</h3>
                    </div>
                    <span class="arrow"> → </span>
                </a>

                <a href="" class="vehicles action-card">
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
                    <img class="rating-icon" src="{{ asset('images/icons/icons8-tour-50.png') }}">
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