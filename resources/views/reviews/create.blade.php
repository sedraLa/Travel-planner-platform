<x-app-layout>
    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/transport.css') }}">
    <link rel="stylesheet" href="{{ asset('css/vehicles.css') }}">
@endpush

    <div class="max-w-xl mx-auto p-6">

        <h2 class="text-xl font-bold mb-4">Submit Review</h2>

        <form method="POST" action="{{ route('reviews.store') }}">
            @csrf

            <input type="hidden" name="type" value="{{ $type }}">
            <input type="hidden" name="id" value="{{ $id }}">

            <div class="mb-4">
                <label class="block mb-1">Rating</label>
                <select name="rating" class="w-full border rounded p-2">
                    <option value="5">⭐ 5</option>
                    <option value="4">⭐ 4</option>
                    <option value="3">⭐ 3</option>
                    <option value="2">⭐ 2</option>
                    <option value="1">⭐ 1</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block mb-1">Review</label>
                <textarea name="review" class="w-full border rounded p-2"></textarea>
            </div>

            <button class="bg-green-600 text-white px-4 py-2 rounded">
                Submit
            </button>
        </form>

    </div>
</x-app-layout>