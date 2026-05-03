<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/transport.css') }}">
        <link rel="stylesheet" href="{{ asset('css/vehicles.css') }}">
    @endpush

    <div class="vehicle-form-container">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Edit Room Types — {{ $hotel->name }}</h2>
            <a href="{{ route('hotels.edit', $hotel->id) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                ← Back to Edit Hotel
            </a>
        </div>

        <form action="{{ route('hotels.room-types.update', $hotel->id) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            @if ($errors->any())
                <div class="mb-4 px-4 py-3 bg-red-100 text-red-800 rounded">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mt-2">
                <h3 class="text-xl font-semibold text-gray-800 mb-2"> Room Types</h3>
                <p class="text-sm text-gray-500 mb-4">Manage existing room types, images, and add new ones.</p>
                <div id="room-types-container" class="space-y-6"></div>
                <button type="button" id="add-room-type-btn" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    + Add Room Type
                </button>
            </div>

            <div class="popup-buttons mt-8">
                <button type="submit" class="btn btn-primary">Update Room Types</button>
                <a href="{{ route('hotels.edit', $hotel->id) }}" class="cancel-btn">Cancel</a>
            </div>
        </form>
    </div>

    @php
    $existingRoomTypes = old('room_types',
        $hotel->roomTypes->map(function ($roomType) {
            return [
                'id' => $roomType->id,
                'name' => $roomType->name,
                'price_per_night' => $roomType->price_per_night,
                'capacity' => $roomType->capacity,
                'quantity' => $roomType->quantity,
                'description' => $roomType->description,
                'amenities' => is_array($roomType->amenities)
                    ? implode(', ', $roomType->amenities)
                    : '',
                'is_refundable' => $roomType->is_refundable,
                'images' => $roomType->images->map(function ($image) {
                    return [
                        'id' => $image->id,
                        'image_url' => $image->image_url,
                        'is_primary' => $image->is_primary,
                    ];
                })->values()->all(),
            ];
        })->values()->all()
    );
    @endphp

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const container = document.getElementById('room-types-container');
            const addBtn = document.getElementById('add-room-type-btn');
            let roomTypeIndex = 0;

            const existingRoomTypes = @json($existingRoomTypes);

            const roomTypeTemplate = (index, values = {}) => {
                const existingImages = Array.isArray(values.images) ? values.images : [];

                const existingImagesHtml = existingImages.length
                    ? `<div class="mt-4">
                        <p class="text-sm text-gray-700 mb-2">Existing Images</p>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            ${existingImages.map(image => `
                                <div class="border rounded-lg p-2">
                                    <img src="/storage/${image.image_url}" class="w-full h-20 object-cover rounded">
                                    <div class="mt-2 space-y-2">
                                        <label class="text-xs block">
                                            <input type="radio"
                                                name="room_types[${index}][primary_image_choice]"
                                                value="existing:${image.id}"
                                                ${image.is_primary ? 'checked' : ''}>
                                            Primary
                                        </label>
                                        <label class="text-xs block text-red-700">
                                            <input type="checkbox"
                                                name="room_types[${index}][remove_existing_image_ids][]"
                                                value="${image.id}">
                                            Remove image
                                        </label>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    </div>`
                    : '';

                return `
                <div class="border rounded-xl p-5 bg-white room-type-card space-y-3">
                    <input type="hidden" name="room_types[${index}][id]" value="${values.id ?? ''}">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm text-gray-600">Room Type Name</label>
                            <input type="text" name="room_types[${index}][name]"
                                value="${values.name ?? ''}" placeholder="Name" class="mt-1 w-full border rounded-md" required>
                        </div>

                        <div>
                            <label class="text-sm text-gray-600">Price Per Night</label>
                            <input type="number" step="0.01" min="0.01" name="room_types[${index}][price_per_night]"
                                value="${values.price_per_night ?? ''}" placeholder="Price" class="mt-1 w-full border rounded-md" required>
                        </div>

                        <div>
                            <label class="text-sm text-gray-600">Capacity</label>
                            <input type="number" min="1" name="room_types[${index}][capacity]"
                                value="${values.capacity ?? ''}" placeholder="Capacity" class="mt-1 w-full border rounded-md" required>
                        </div>

                        <div>
                            <label class="text-sm text-gray-600">Quantity</label>
                            <input type="number" min="0" name="room_types[${index}][quantity]"
                                value="${values.quantity ?? ''}" placeholder="Quantity" class="mt-1 w-full border rounded-md" required>
                        </div>
                    </div>

                    <div>
                        <label class="text-sm text-gray-600">Description</label>
                        <textarea name="room_types[${index}][description]" class="mt-1 w-full border rounded-md">${values.description ?? ''}</textarea>
                    </div>

                    <div>
                        <label class="text-sm text-gray-600">Amenities (comma separated)</label>
                        <input type="text" name="room_types[${index}][amenities]"
                            value="${values.amenities ?? ''}" class="mt-1 w-full border rounded-md" placeholder="Wifi, AC, Balcony">
                    </div>

                    <label class="text-sm text-gray-700 block">
                        <input type="checkbox" name="room_types[${index}][is_refundable]" value="1" ${values.is_refundable ? 'checked' : ''}>
                        Refundable
                    </label>

                    ${existingImagesHtml}

                    <div>
                        <label class="text-sm text-gray-600">Upload New Images</label>
                        <input type="file" name="room_types[${index}][images][]" multiple class="mt-1 block w-full text-sm text-gray-500">
                        <p class="text-xs text-gray-500 mt-1">After upload, choose primary image by selecting one of existing images or by new index in backend default (first image).</p>
                    </div>
                </div>`;
            };

            function addRoomType(values = {}) {
                const wrapper = document.createElement('div');
                wrapper.innerHTML = roomTypeTemplate(roomTypeIndex, values);
                container.appendChild(wrapper.firstElementChild);
                roomTypeIndex++;
            }

            if (existingRoomTypes.length) {
                existingRoomTypes.forEach(rt => addRoomType(rt));
            } else {
                addRoomType();
            }

            addBtn.addEventListener('click', () => addRoomType());
        });
    </script>
</x-app-layout>
