<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/transport.css') }}">
        <link rel="stylesheet" href="{{ asset('css/vehicles.css') }}">
    @endpush

    <div class="vehicle-form-container">
        <h2>Add New Activity</h2>

        <form action="{{ route('activities.store') }}" method="post" enctype="multipart/form-data">
            @csrf

            @if ($errors->any())
                <div class="mb-4 px-4 py-3 bg-red-100 text-red-800 rounded">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="first-section">
                <div class="left">
                    <x-input-label for="name" value="Activity Name" />
                    <x-text-input id="name" type="text" name="name" :value="old('name')" required
                        placeholder="Enter activity name" />

                    <x-input-label for="destination_id" value="Select Destination" />
                    <select id="destination_id" name="destination_id"
                        class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        required>
                        <option value="">-- Choose a destination --</option>
                        @foreach ($destinations as $destination)
                            <option value="{{ $destination->id }}">{{ $destination->name }}</option>
                        @endforeach
                    </select>

                    <x-input-label for="duration" value="Duration" />
                    <x-text-input id="duration" type="number" step="0.1" name="duration" :value="old('duration')"
                        placeholder="e.g. 2" />

                    <x-input-label for="duration_unit" value="Duration Unit" />
                    <select id="duration_unit" name="duration_unit"
                        class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        required>
                        <option value="minutes">Minutes</option>
                        <option value="hours" selected>Hours</option>
                        <option value="days">Days</option>
                    </select>
                </div>

                <div class="right">
                    <x-input-label for="price" value="Price" />
                    <x-text-input id="price" type="number" step="0.01" name="price" :value="old('price')"
                        placeholder="e.g. 50.00" />

                    <select id="category" name="category" required>
                        <option value="">-- Choose category --</option>
                        @foreach (\App\Enums\Category::cases() as $cat)
                            <option value="{{ $cat->value }}" {{ old('category') == $cat->value ? 'selected' : '' }}>
                                {{ ucfirst($cat->value) }}
                            </option>
                        @endforeach
                    </select>

                    <x-input-label for="is_active" value="Is Active" />
                    <select id="is_active" name="is_active"
                        class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="1" selected>Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>

            <x-input-label for="description" value="Description" />
            <textarea id="description" name="description" rows="4"
                class="block w-full border-gray-300 rounded-md shadow-sm"
                placeholder="Enter activity description">{{ old('description') }}</textarea>

            <x-input-label for="image" value="Activity Image" />
            <input type="file" id="image" name="image" accept="image/*">

            <div class="popup-buttons mt-4">
                <button type="submit" class="btn btn-primary">Save</button>
                <a href="{{ route('activities.index') }}" class="cancel-btn">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>