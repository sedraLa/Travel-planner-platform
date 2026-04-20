@php use App\Enums\UserRole; @endphp
<x-app-layout>
@push('styles')  
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/activityindex.css') }}"> 
    @endpush

    <div>

        {{-- ══════ HERO ══════ --}}
        <section class="act-hero">

            {{-- 📌 ضع مسار صورتك هنا --}}
            <img
                class="act-hero__img"
                src="{{ asset('images/activity.jpg') }}"
                alt="Activities"
            >

            <div class="act-hero__content">

                @if (Auth::user()->role === UserRole::USER->value)
                    <a href="{{ route('destination.index') }}" class="act-hero__back">
                        ← Back to Destinations
                    </a>
                @endif

                <h1 class="act-hero__title">Activities</h1>
                <p class="act-hero__sub">Choose from our curated selection of premium activities</p>



                @if(Auth::user()->role === UserRole::ADMIN->value)
                    <a href="{{ route('activities.create') }}" class="act-hero__add">
                        + Add New Activity
                    </a>
                @endif

                <form method="GET" action="{{ route('activities.index') }}" style="width:100%;display:flex;justify-content:center;">
                        <div class="act-hero__search">
                            <input
                                type="text"
                                name="search"
                                placeholder="Search by activity or destination"
                                value="{{ request('search') }}"
                            >
                            <button type="submit">Search</button>
                        </div>
                    

            </div>
        </section>

        {{-- ══════ FILTERS ══════ --}}
        <div class="act-filters">
            <div class="act-filters__form">

                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif

                @if(Auth::user()->role === \App\Enums\UserRole::ADMIN->value)
                <select name="availability">
                    <option value="">Availability</option>
                    <option value="available"   {{ request('availability') == 'available'   ? 'selected' : '' }}>Available</option>
                    <option value="unavailable" {{ request('availability') == 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                </select>
                @endif

                <select name="difficulty">
                    <option value="">Difficulty</option>
                    <option value="easy"     {{ request('difficulty') == 'easy'     ? 'selected' : '' }}>Easy</option>
                    <option value="moderate" {{ request('difficulty') == 'moderate' ? 'selected' : '' }}>Moderate</option>
                    <option value="hard"     {{ request('difficulty') == 'hard'     ? 'selected' : '' }}>Hard</option>
                </select>

               

                <select name="requires_booking">
                    <option value="">Booking</option>
                    <option value="1" {{ request('requires_booking') == '1' ? 'selected' : '' }}>Required</option>
                    <option value="0" {{ request('requires_booking') == '0' ? 'selected' : '' }}>Not required</option>
                </select>

                <select name="family_friendly">
                    <option value="">Family</option>
                    <option value="yes" {{ request('family_friendly') == 'yes' ? 'selected' : '' }}>Friendly</option>
                    <option value="no"  {{ request('family_friendly') == 'no'  ? 'selected' : '' }}>Not friendly</option>
                </select>

                <select name="pets_allowed">
                    <option value="">Pets</option>
                    <option value="1" {{ request('pets_allowed') == '1' ? 'selected' : '' }}>Allowed</option>
                    <option value="0" {{ request('pets_allowed') == '0' ? 'selected' : '' }}>Not allowed</option>
                </select>

                <select name="category">
                    <option value="">Category</option>
                    @foreach (\App\Enums\Category::cases() as $cat)
                        <option value="{{ $cat->value }}" {{ request('category') == $cat->value ? 'selected' : '' }}>
                            {{ ucfirst($cat->value) }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="act-filters__apply">Filter</button>
                <a href="{{ route('activities.index') }}" class="act-filters__reset">Reset</a>
                </div>
               
            </form>
        </div>

        {{-- Success --}}
        @if (session('success'))
            <div class="act-success">{{ session('success') }}</div>
        @endif

        {{-- ══════ CARDS ══════ --}}
        <div class="act-main">
            <div class="act-cards">

                @forelse($activities as $activity)
                    <div class="act-card">
                        <div class="act-card__img-wrap">
                            <img src="{{ asset('storage/' . $activity->image) }}" alt="{{ $activity->name }}">

                            @if (Auth::user()->role === UserRole::USER->value)
                                <button
                                    class="act-card__fav fav-toggle"
                                    data-url="{{ route('favorites.add', ['type' => 'activity', 'id' => $activity->id]) }}"
                                    aria-label="Toggle favourite"
                                >
                                    @if(auth()->user()->favoriteActivities->pluck('id')->contains($activity->id))
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
                            <h3 class="act-card__name">{{ $activity->name }}</h3>
                            <span class="act-card__badge">{{ $activity->category }}</span>

                            <div class="act-card__actions">
                                @if(Auth::user()->role === UserRole::ADMIN->value)
                                    <a href="{{ route('activities.edit', $activity->id) }}" class="btn-edit-c">Edit</a>
                                    <form action="{{ route('activities.destroy', $activity->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-delete-c" onclick="return confirm('Delete this activity?')">Delete</button>
                                    </form>
                                @else
                                    <a href="{{ route('activity.reservations.form', $activity) }}">
                                        <button class="btn-book">Book</button>
                                    </a>
                                @endif

                                <a href="{{ route('Activity.show', $activity) }}" class="btn-details">Details</a>
                            </div>
                        </div>

                    </div>
                @empty
                    <p class="act-empty">No activities found.</p>
                @endforelse

            </div>

            <div class="act-pagination">
                {{ $activities->appends(request()->query())->links() }}
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

</x-app-layout>
