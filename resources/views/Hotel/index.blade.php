@php use App\Enums\UserRole; @endphp

<x-app-layout>
@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/hotel.css') }}">
@endpush

<div>

    {{-- ══════ HERO ══════ --}}
    <section class="act-hero">

        <img class="act-hero__img" src="{{ asset('images/h10.jpg') }}" alt="Hotels">

        <div class="act-hero__content">

            @if(Auth::user()->role === UserRole::USER->value)
                <a href="{{ route('destination.index') }}" class="act-hero__back">
                    ← Back to Destinations
                </a>
            @endif

            <h1 class="act-hero__title">Hotels</h1>
            <p class="act-hero__sub">Discover our curated selection of premium hotels</p>

            @if(Auth::user()->role === UserRole::ADMIN->value)
                <a href="{{ route('hotels.create') }}" class="act-hero__add">
                    + Add New Hotel
                </a>
            @endif

            <form method="GET" action="{{ route('hotels.index') }}" style="width:100%;display:flex;justify-content:center;">
                <div class="act-hero__search">
                    <input
                        type="text"
                        name="search"
                        placeholder="Search hotels by name or city"
                        value="{{ request('search') }}"
                    >
                    <button type="submit">Search</button>
                </div>
            

        </div>
    </section>

    {{-- ══════ FILTERS ══════ --}}
    <div class="act-filters">
        <div class="act-filters__form" >

            @if(request('search'))
                <input type="hidden" name="search" value="{{ request('search') }}">
            @endif

            @if(request('destination_id'))
                <input type="hidden" name="destination_id" value="{{ request('destination_id') }}">
            @endif

            <select name="city">
                <option value="">City</option>
                @foreach(\App\Models\Hotel::distinct('city')->pluck('city') as $city)
                    <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>{{ $city }}</option>
                @endforeach
            </select>

            <select name="country">
                <option value="">Country</option>
                @foreach(\App\Models\Hotel::distinct('country')->pluck('country') as $country)
                    <option value="{{ $country }}" {{ request('country') == $country ? 'selected' : '' }}>{{ $country }}</option>
                @endforeach
            </select>

            <select name="stars">
                <option value="">Stars</option>
                @for($i = 1; $i <= 5; $i++)
                    <option value="{{ $i }}" {{ request('stars') == $i ? 'selected' : '' }}>{{ $i }} ★</option>
                @endfor
            </select>

            <div class="dropdown">
                <button type="button" class="dropbtn">Select Amenities ▾</button>
                <div class="dropdown-content">
                    @foreach(['Wifi','Parking','Pool','Spa','Restaurant','Gym','Laundry','Air Condition','Free Breakfast'] as $amenity)
                        <label>
                            <input type="checkbox" name="amenities[]" value="{{ $amenity }}"
                                {{ in_array($amenity, (array) request('amenities', [])) ? 'checked' : '' }}>
                            {{ $amenity }}
                        </label>
                    @endforeach
                </div>
            </div>

            <select name="pets_allowed">
                <option value="">Pets</option>
                <option value="1" {{ request('pets_allowed') == '1' ? 'selected' : '' }}>Allowed</option>
                <option value="0" {{ request('pets_allowed') == '0' ? 'selected' : '' }}>Not allowed</option>
            </select>

            <input type="number" name="min_price" placeholder="Min Price" value="{{ request('min_price') }}">
            <input type="number" name="max_price" placeholder="Max Price" value="{{ request('max_price') }}">

            <button type="submit" class="act-filters__apply">Filter</button>
            <a href="{{ route('hotels.index') }}" class="act-filters__reset">Reset</a>

         </div>
    </div>
</form>

    {{-- Success --}}
    @if(session('success'))
        <div class="act-success">{{ session('success') }}</div>
    @endif

    {{-- ══════ CARDS ══════ --}}
    <div class="act-main">
        <div class="act-cards">

            @forelse($hotels as $hotel)
                <div class="act-card">
                    <div class="act-card__img-wrap">
                        <img
                            src="{{ asset('storage/' . optional($hotel->images->where('is_primary', true)->first())->image_url) }}"
                            alt="{{ $hotel->name }}"
                        >

                        @if($hotel->stars)
                            <div class="act-card__stars">
                                @for($i = 0; $i < $hotel->stars; $i++)★@endfor
                            </div>
                        @endif

                        @if(Auth::user()->role === UserRole::USER->value)
                            <button
                                class="act-card__fav fav-toggle"
                                data-url="{{ route('favorites.add', ['type' => 'hotel', 'id' => $hotel->id]) }}"
                                aria-label="Toggle favourite"
                            >
                                @if(auth()->user()->favoriteHotels->contains('id', $hotel->id))
                                    <svg class="heart-filled" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                                    </svg>
                                @else
                                    <svg class="heart-empty" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                    </svg>
                                @endif
                            </button>
                        @endif
                    </div>

                    <div class="act-card__body">
                        <h3 class="act-card__name">{{ $hotel->name }}</h3>

                        <div class="act-card__location">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                            {{ $hotel->city }}, {{ $hotel->country }}
                        </div>

                        <span class="act-card__badge">{{ $hotel->stars ?? '–' }} ★</span>

                        <div class="act-card__actions">
                            @if(Auth::user()->role === UserRole::ADMIN->value)
                                <a href="{{ route('hotels.edit', $hotel) }}" class="btn-edit-c">Edit</a>
                                <form action="{{ route('hotels.destroy', $hotel->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-delete-c" onclick="return confirm('Delete this hotel?')">Delete</button>
                                </form>
                            @else
                                <a href="{{ route('reservations.form', $hotel) }}">
                                    <button class="btn-book">Book</button>
                                </a>
                            @endif

                            <a href="{{ route('hotel.show', $hotel->id) }}" class="btn-details">Details</a>
                        </div>
                    </div>
                </div>
            @empty
                <p class="act-empty">No hotels found.</p>
            @endforelse

        </div>

        <div class="act-pagination">
            {{ $hotels->appends(request()->query())->links() }}
        </div>
    </div>

</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    document.addEventListener("click", async (e) => {
        const btn = e.target.closest(".fav-toggle");
        if (!btn) return;

        btn.classList.remove("pop");
        void btn.offsetWidth;
        btn.classList.add("pop");

        try {
            const res = await fetch(btn.dataset.url, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Accept": "application/json"
                }
            });

            const data = await res.json();
            const added = data.status === "added";

            btn.innerHTML = added
                ? `<svg class="heart-filled" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                       <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                   </svg>`
                : `<svg class="heart-empty" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                       <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                   </svg>`;

        } catch (err) {
            console.error(err);
        }
    });
});
</script>





<script>
document.addEventListener("DOMContentLoaded", function () {
    const dropdown = document.querySelector(".act-filters .dropdown");
    const button = dropdown.querySelector(".dropbtn");
    const content = dropdown.querySelector(".dropdown-content");

    button.addEventListener("click", function (e) {
        e.stopPropagation();
        dropdown.classList.toggle("open");
    });

    content.addEventListener("click", function (e) {
        e.stopPropagation();
    });

    document.addEventListener("click", function () {
        dropdown.classList.remove("open");
    });
});
</script>

</x-app-layout>