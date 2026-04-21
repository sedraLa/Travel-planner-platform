@push('styles')
<link rel="stylesheet" href="{{asset('css/vehicles.css')}}">
<link rel="stylesheet" href="{{ asset('css/transport.css') }}">
@endpush

<x-app-layout>

<div class="main-wrapper p-6 md:p-8"> 

    {{-- Search Form --}}
    <form method="GET" action="{{ route('admin.reviews.index') }}" class="flex flex-wrap gap-4 items-end mb-6">

        <div class="flex-1 min-w-[200px]">
            <label class="text-sm text-gray-600">Search User</label>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="User name..."
                   class="w-full border rounded-lg p-2">
        </div>

        <div>
            <label class="text-sm text-gray-600">Type</label>
            <select name="type" class="border rounded-lg p-2" style="margin-bottom:0">
                <option value="">All</option>
                <option value="hotel" @selected(request('type')=='hotel')>Hotel</option>
                <option value="trip" @selected(request('type')=='trip')>Trip</option>
                <option value="guide" @selected(request('type')=='guide')>Guide</option>
                <option value="driver" @selected(request('type')=='driver')>Driver</option>
            </select>
        </div>

        <div>
            <label class="text-sm text-gray-600">Rating</label>
            <select name="rating" class="border rounded-lg p-2" style="margin-bottom:0">
                <option value="">All</option>
                @for($i=1; $i<=5; $i++)
                    <option value="{{ $i }}" @selected(request('rating')==$i)>
                        {{ $i }} ⭐
                    </option>
                @endfor
            </select>
        </div>

        <div class="flex gap-2">
            <button class="bg-blue-600 text-white px-4 py-2 rounded-lg">
                Filter
            </button>
            <a href="{{ route('admin.reviews.index') }}"
               class="px-4 py-2 border rounded-lg text-gray-600">
               Reset
            </a>
        </div>
    </form>

    {{-- Reviews Table --}}
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">

        {{-- Header --}}
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-2xl font-bold text-gray-800">Reviews List</h2>
            <p class="text-gray-500 mt-1">All user reviews across the system.</p>

            {{-- Success --}}
            @if (session('success'))
                <div class="mt-4 px-4 py-3 bg-green-100 text-green-800 rounded">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Error --}}
            @if (session('error'))
                <div class="mt-4 px-4 py-3 bg-red-100 text-red-700 rounded">
                    {{ session('error') }}
                </div>
            @endif
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">

                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rating</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Review</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Action</th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-200">

                    @forelse($reviews as $review)
                        <tr>

                            {{-- User --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $review->user->name ?? '—' }}
                                </div>
                            </td>

                            {{-- Type --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 text-xs rounded-full bg-blue-100 text-blue-700">
                                    {{ class_basename($review->reviewable_type) }}
                                </span>
                            </td>

                            {{-- Item --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $review->reviewable->name ?? $review->reviewable->title ?? '—' }}
                                </div>
                            </td>

                            {{-- Rating --}}
                            <td class="px-6 py-4 whitespace-nowrap text-yellow-500 font-semibold">
                                {{ $review->rating }} ⭐
                            </td>

                            {{-- Review --}}
                            <td class="px-6 py-4 max-w-xs truncate text-sm text-gray-700">
                                {{ $review->review }}
                            </td>

                            {{-- Action --}}
                            <td class="px-6 py-4 text-center">
                                <form action="{{ route('reviews.destroy', $review->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Are you sure you want to delete this review?');">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="icon-btn">
                                        <img src="{{ asset('images/icons/delete.png') }}"
                                             class="w-6 h-6 object-contain">
                                    </button>
                                </form>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                No reviews found.
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="p-4">
            {{ $reviews->links() }}
        </div>

    </div>

</div>

</x-app-layout>