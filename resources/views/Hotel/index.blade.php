@php use App\Enums\UserRole; @endphp

<x-app-layout>
    @push('styles')
    <link rel="stylesheet" href="{{asset('css/destinations.css')}}">
@endpush

    <div class="main-wrapper">
        @if (Auth::user()->role === UserRole::ADMIN->value)
            <!-- Create Hotel Button -->
            <div class="flex justify-end mb-4 px-6 pt-6">
                <a href="{{ route('hotels.create') }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded shadow transition duration-200">
                    + Add New Hotel
                </a>
            </div>

            @if (session('success'))
                <div class="mb-4 px-4 py-3 bg-green-100 text-green-800 rounded">
                    {{ session('success') }}
                </div>
            @endif
        @endif

        <!-- Hero Background (for all users) -->
        <div class="hero-background hotels-page"></div>

        <!-- Search Form -->
        <form class="search-form" method="GET" action="{{ route('hotels.index') }}">
            <h1>Find Your Hotel</h1>
            <div class="search-container">
                <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" class="h-8 w-8 text-red-500"
                    viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                </svg>
                <input type="search" name="search" class="search-input" placeholder="Search hotels..." required />
                <button type="submit" class="search-button">Search</button>
            </div>
        </form>

        <!-- Hotels Cards -->
        <div class="cards">
            @forelse ($hotels as $hotel)
                <div class="card">
                        <div class="card-img">
                    {{-- Add heart button to add to favourites--}}
                    @if (Auth::user()->role === UserRole::USER->value)
                     <button class="fav-btn fav-toggle"
                            data-url="{{ route('favorites.add', ['type' => 'hotel', 'id' => $hotel->id]) }}"
                            style="position:absolute; top:10px; right:10px; z-index:10; background:none; border:none; cursor:pointer;">

                            @if(auth()->user()->favoriteHotels->contains('id', $hotel->id))
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-500"
                            viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                           d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"
                           clip-rule="evenodd"/>
                    </svg>
                    @else
                             {{-- قلب فاضي --}}
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-500"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round"
                          d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                    @endif

                         </button>
                    @endif
                <a href="{{ route('hotel.show', $hotel->id) }}">
                    <img src="{{ asset('storage/' . optional($hotel->images->where('is_primary', true)->first())->image_url) }}">
                    </a>
                </div>

                <a href="{{ route('hotel.show', $hotel->id) }}">
                    <h5>{{ $hotel->name }}</h5>
                    <p class="overview">{{ Str::limit($hotel->address, 80) }}</p>
                </a>

                @if(Auth::user()->role === UserRole::ADMIN->value)
                                        <div class="manage-btn flex items-center gap-3 mt-3 mb-3 px-4" >
                                            <a href="{{route('hotels.edit',$hotel)}}">
                                            <button class="px-4 py-2 rounded-xl text-white bg-blue-600 hover:bg-blue-700 transition duration-200 text-sm shadow-sm">Edit </button></a>


                                            <form action="{{route('hotels.destroy',$hotel->id)}}" method="post"
                                                onsubmit="return confirm('Are you sure you want to delete this Hotel?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"  class="px-4 py-2 rounded-xl text-white bg-red-600 hover:bg-red-700 transition duration-200 text-sm shadow-sm" >Delete</button>
                                            </form>
                                        </div>
                                    @endif
            </div>
            @empty
                <p style="text-align:center;">No hotels found.</p>
            @endforelse
        </div>

        <div class="pagination-wrapper">
            {{ $hotels->appends(request()->query())->links() }}
        </div>

</div>

</x-app-layout>
<script>
document.addEventListener("DOMContentLoaded", () => {

    document.addEventListener("click", async (e) => {
        let btn = e.target.closest(".fav-toggle");
        if (!btn) return;

        let icon = btn.querySelector("svg");

        try {
            let res = await fetch(btn.dataset.url, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Accept": "application/json"
                }
            });

            let data = await res.json();

            let isAdded = data.status === "added";
            icon.classList.toggle("text-red-500", isAdded);
            icon.classList.toggle("text-gray-500", !isAdded);
            icon.setAttribute("fill", isAdded ? "currentColor" : "none");

        } catch (err) {
            console.error(err);
        }
    });

});
</script>
