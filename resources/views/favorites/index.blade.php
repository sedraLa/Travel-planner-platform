<x-app-layout>
    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/favorites.css') }}">
     <link rel="stylesheet" href="{{ asset('css/transport.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600&family=DM+Sans:wght@400;500&display=swap" rel="stylesheet">
@endpush

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Favorites') }}
        </h2>
    </x-slot>

    <div class="fav-page">

        {{-- Hero --}}
        <div class="fav-hero">
            <h1>
                <svg class="heart-icon" viewBox="0 0 24 24" fill="#F43F5E">
                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                </svg>
                My Favorites
            </h1>
            <p>All the places and experiences you love, in one place</p>
        </div>

        {{-- Stats Bar --}}
        <div class="stats-bar">
            <div class="stat-card s1">
                <div class="num">{{ $destinations->count() }}</div>
                <div class="lbl">Destinations</div>
            </div>
            <div class="stat-card s2">
                <div class="num">{{ $hotels->count() }}</div>
                <div class="lbl">Hotels</div>
            </div>
            <div class="stat-card s3">
                <div class="num">{{ $trips->count() }}</div>
                <div class="lbl">Trips</div>
            </div>
            <div class="stat-card s4">
                <div class="num">{{ $activities->count() }}</div>
                <div class="lbl">Activities</div>
            </div>
        </div>

        {{-- Favorite Destinations --}}
        <div class="section-block">
            <div class="section-header">
                <div class="section-title">
                    <div class="section-dot blue"></div>
                    <h2>Favorite Destinations</h2>
                </div>
                <span class="section-count">{{ $destinations->count() }} saved</span>
            </div>

            @if($destinations->isNotEmpty())
                <div class="cards-grid">
                    @foreach ($destinations as $destination)
                        @php $primaryImage = $destination->images->where('is_primary', true)->first(); @endphp
                        <a href="{{ route('destination.show', $destination->id) }}" class="fav-card fav-badge">
                            @if($primaryImage)
                                <img src="{{ asset('storage/' . $primaryImage->image_url) }}" alt="{{ $destination->name }}" class="card-img">
                            @else
                                <div class="card-img-placeholder dest">🗺️</div>
                            @endif
                            <div class="card-body">
                                <span class="card-tag tag-dest">Destination</span>
                                <h4>{{ $destination->name }}</h4>
                                <p>{{ $destination->city ?? 'Explore now' }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <p>You haven't added any favorite destinations yet.</p>
                </div>
            @endif
        </div>

        {{-- Favorite Hotels --}}
        <div class="section-block">
            <div class="section-header">
                <div class="section-title">
                    <div class="section-dot indigo"></div>
                    <h2>Favorite Hotels</h2>
                </div>
                <span class="section-count">{{ $hotels->count() }} saved</span>
            </div>

            @if($hotels->isNotEmpty())
                <div class="cards-grid">
                    @foreach ($hotels as $hotel)
                        @php $primaryImage = $hotel->images->where('is_primary', true)->first(); @endphp
                        <a href="{{ route('hotel.show', $hotel->id) }}" class="fav-card fav-badge">
                            @if($primaryImage)
                                <img src="{{ asset('storage/' . $primaryImage->image_url) }}" alt="{{ $hotel->name }}" class="card-img">
                            @else
                                <div class="card-img-placeholder hotel">🏨</div>
                            @endif
                            <div class="card-body">
                                <span class="card-tag tag-hotel">Hotel</span>
                                <h4>{{ $hotel->name }}</h4>
                                <p>{{ $hotel->city ?? 'Book now' }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <p>You haven't added any favorite hotels yet.</p>
                </div>
            @endif
        </div>

        {{-- Favorite Trips --}}
        <div class="section-block">
            <div class="section-header">
                <div class="section-title">
                    <div class="section-dot amber"></div>
                    <h2>Favorite Trips</h2>
                </div>
                <span class="section-count">{{ $trips->count() }} saved</span>
            </div>

            @if($trips->isNotEmpty())
                <div class="cards-grid">
                    @foreach ($trips as $trip)
                        @php $primaryImage = $trip->images->where('is_primary', true)->first(); @endphp
                        <a href="{{ route('user.trips.show', $trip) }}" class="fav-card fav-badge">
                            @if($primaryImage)
                                <img src="{{ asset('storage/' . $primaryImage->image_url) }}" alt="{{ $trip->name }}" class="card-img">
                            @else
                                <div class="card-img-placeholder trip">✈️</div>
                            @endif
                            <div class="card-body">
                                <span class="card-tag tag-trip">Trip</span>
                                <h4>{{ $trip->name }}</h4>
                                <p>{{ $trip->city ?? 'Explore now' }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <p>You haven't added any favorite trips yet.</p>
                </div>
            @endif
        </div>

        {{-- Favorite Activities --}}
        <div class="section-block">
            <div class="section-header">
                <div class="section-title">
                    <div class="section-dot rose"></div>
                    <h2>Favorite Activities</h2>
                </div>
                <span class="section-count">{{ $activities->count() }} saved</span>
            </div>

            @if($activities->isNotEmpty())
                <div class="cards-grid">
                    @foreach ($activities as $activity)
                        <a href="{{ route('Activity.show', $activity) }}" class="fav-card fav-badge">
                            @if($activity->image)
                                <img src="{{ asset('storage/' . $activity->image) }}" alt="{{ $activity->name }}" class="card-img">
                            @else
                                <div class="card-img-placeholder act">🎯</div>
                            @endif
                            <div class="card-body">
                                <span class="card-tag tag-act">Activity</span>
                                <h4>{{ $activity->name }}</h4>
                                <p>{{ $activity->destination->city ?? 'Explore now' }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <p>You haven't added any favorite activity yet.</p>
                </div>
            @endif
        </div>

    </div>
</x-app-layout>
