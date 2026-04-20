<x-app-layout>
    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/transport.css') }}"> 
    <link rel="stylesheet" href="{{ asset('css/vehicles.css') }}">
    @endpush
    
    <div class="vehicle-form-container">
    
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-800">
                Room Types - {{ $hotel->name }}
            </h2>
    
            <a href="{{ route('hotels.index') }}"
               class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                ← Back
            </a>
        </div>
    
        @if (session('success'))
            <div class="mb-4 px-4 py-3 bg-green-100 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif
    
        <form method="POST"
              action="{{ route('room-types.update', $hotel->id) }}"
              enctype="multipart/form-data">
    
            @csrf
            @method('PUT')
    
            <div id="room-container"></div>
    
            <button type="button"
                    id="add"
                    class="mt-6 px-4 py-2 bg-blue-600 text-white rounded-md">
                + Add Room Type
            </button>
    
            <div class="mt-6">
                <button type="submit"
                        class="px-6 py-3 bg-green-600 text-white rounded-md">
                    Save
                </button>
            </div>
    
        </form>
    </div>
    
    @php
    $rooms = old('room_types', $hotel->roomTypes->map(function ($r) {
        return [
            'id' => $r->id,
            'name' => $r->name,
            'price_per_night' => $r->price_per_night,
            'capacity' => $r->capacity,
            'quantity' => $r->quantity,
            'images' => $r->images
        ];
    })->toArray());
    @endphp
    
    <script>
    const existing = @json($rooms);
    let i = 0;

    function removeRoomType(card) {
        card.remove();
    }

    function handleFiles(input, index) {
        const preview = document.getElementById(`preview-${index}`);
        const select = document.getElementById(`primary-${index}`);
        const files = Array.from(input.files || []);

        preview.innerHTML = '';
        select.innerHTML = `<option value="">Select primary image</option>`;

        files.forEach((file, idx) => {
            const reader = new FileReader();
            reader.onload = e => {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = "h-20 w-full object-cover rounded mb-1";
                preview.appendChild(img);
            };
            reader.readAsDataURL(file);

            const opt = document.createElement('option');
            opt.value = `${idx}`;
            opt.text = file.name;
            select.appendChild(opt);
        });
    }
    
    function template(i, r = {}) {
    
        return `
        <div class="border rounded-xl p-5 bg-white mb-6 room-type-card">

            <input type="hidden" name="room_types[${i}][id]" value="${r.id ?? ''}">

            <div class="flex justify-end mb-3">
                <button type="button"
                        class="px-3 py-1.5 text-sm bg-red-600 text-white rounded-md hover:bg-red-700"
                        onclick="removeRoomType(this.closest('.room-type-card'))">
                    Remove
                </button>
            </div>
    
            <label class="block font-semibold">Room Name</label>
            <input class="border rounded-md w-full mb-3 p-2"
                name="room_types[${i}][name]"
                value="${r.name ?? ''}">
    
            <label class="block font-semibold">Price</label>
            <input class="border rounded-md w-full mb-3 p-2"
                name="room_types[${i}][price_per_night]"
                value="${r.price_per_night ?? ''}">
    
            <label class="block font-semibold">Capacity</label>
            <input class="border rounded-md w-full mb-3 p-2"
                name="room_types[${i}][capacity]"
                value="${r.capacity ?? ''}">
    
            <label class="block font-semibold">Quantity</label>
            <input class="border rounded-md w-full mb-3 p-2"
                name="room_types[${i}][quantity]"
                value="${r.quantity ?? ''}">
    
            {{-- EXISTING IMAGES --}}
            ${r.images && r.images.length ? `
            <div class="mb-4">
                <label class="font-semibold">Existing Images</label>
    
                <div class="grid grid-cols-3 gap-3 mt-2">
    
                    ${r.images.map(img => `
                        <div class="border p-2 rounded">
                            <img src="/storage/${img.image_url}"
                                 class="w-full h-24 object-cover rounded">
    
                            <div class="text-xs mt-1">
                                ${img.is_primary ? 'Primary' : ''}
                            </div>
                        </div>
                    `).join('')}
    
                </div>
            </div>
            ` : '' }
    
            {{-- NEW IMAGES --}}
            <label class="block font-semibold">Upload Images</label>
    
            <input type="file"
                name="room_types[${i}][images][]"
                multiple
                class="mb-3"
                onchange="handleFiles(this, ${i})">
    
            <div id="preview-${i}" class="grid grid-cols-3 gap-2 mb-3"></div>
    
            <label class="block font-semibold">Primary Image</label>
    
            <select name="room_types[${i}][primary_new_image_choice]"
                    id="primary-${i}"
                    class="border rounded-md w-full p-2">
                <option value="">Select primary image</option>
            </select>
    
        </div>`;
    }
    
    function add(r = {}) {
        const div = document.createElement('div');
        div.innerHTML = template(i++, r);
        document.getElementById('room-container').appendChild(div.firstElementChild);
    }
    
    if (existing.length) {
        existing.forEach(r => add(r));
    } else {
        add();
    }
    
    document.getElementById('add').onclick = () => add();
    
    </script>
    
    </x-app-layout>
