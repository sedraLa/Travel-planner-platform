<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/transport.css') }}">
        <link rel="stylesheet" href="{{ asset('css/vehicles.css') }}">
    @endpush

    <div class="vehicle-form-container">
        <h2>Edit Activity</h2>

        <form action="{{ route('activities.update', $activity->id) }}" method="post" enctype="multipart/form-data">
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

            <div class="first-section">
                <div class="left">
                    <x-input-label for="name" value="Activity Name" />
                    <x-text-input id="name" type="text" name="name"
                        :value="old('name', $activity->name)" required placeholder="Enter activity name" />

                    <x-input-label for="destination_id" value="Select Destination" />
                    <select id="destination_id" name="destination_id" required
                        class="block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">-- Choose a destination --</option>
                        @foreach ($destinations as $destination)
                            <option value="{{ $destination->id }}"
                                {{ old('destination_id', $activity->destination_id) == $destination->id ? 'selected' : '' }}>
                                {{ $destination->name }}
                            </option>
                        @endforeach
                    </select>

                    <x-input-label for="duration" value="Duration" />
                    <x-text-input id="duration" type="number" step="0.1" name="duration"
                        :value="old('duration', $activity->duration)" placeholder="e.g. 2" />

                    <x-input-label for="duration_unit" value="Duration Unit" />
                    <select id="duration_unit" name="duration_unit" required
                        class="block w-full border-gray-300 rounded-md shadow-sm">
                        @foreach(['minutes','hours','days'] as $unit)
                            <option value="{{ $unit }}"
                                {{ old('duration_unit', $activity->duration_unit) == $unit ? 'selected' : '' }}>
                                {{ ucfirst($unit) }}
                            </option>
                        @endforeach
                    </select>

                    <x-input-label for="start_time" value="Start Time" />
                    <x-text-input id="start_time" type="time" name="start_time"
                        :value="old('start_time', $activity->start_time)" />

                    <x-input-label for="end_time" value="End Time" />
                    <x-text-input id="end_time" type="time" name="end_time"
                        :value="old('end_time', $activity->end_time)" />

                    <x-input-label for="start_date" value="Start Date" />
                    <x-text-input id="start_date" type="date" name="start_date"
                        :value="old('start_date', $activity->start_date)" />

                    <x-input-label for="end_date" value="End Date" />
                    <x-text-input id="end_date" type="date" name="end_date"
                        :value="old('end_date', $activity->end_date)" />

                    <x-input-label for="availability" value="Availability" />
                    <x-text-input id="availability" type="text" name="availability" required
                        :value="old('availability', $activity->availability)" />
                        <x-input-label for="guide_name" value="Guide Name" />
                    <x-text-input id="guide_name" type="text" name="guide_name"
                        :value="old('guide_name', $activity->guide_name)" />

                    <x-input-label for="guide_language" value="Guide Language" />
                    <x-text-input id="guide_language" type="text" name="guide_language"
                        :value="old('guide_language', $activity->guide_language)" />

                    <x-input-label for="contact_number" value="Contact Number" />
                    <x-text-input id="contact_number" type="text" name="contact_number"
                        :value="old('contact_number', $activity->contact_number)" />

                    <x-input-label for="requirements" value="Requirements" />
                    <textarea id="requirements" name="requirements"
                        class="block w-full border-gray-300 rounded-md shadow-sm">{{ old('requirements', $activity->requirements) }}</textarea>
                </div>

                <div class="right">
                    <x-input-label for="price" value="Price" />
                    <x-text-input id="price" type="number" step="0.01" name="price"
                        :value="old('price', $activity->price)" placeholder="e.g. 50.00" />

                    <x-input-label for="category" value="Category" />
                    <select id="category" name="category" required class="block w-full border-gray-300 rounded-md shadow-sm">
                        @foreach (\App\Enums\Category::cases() as $cat)
                            <option value="{{ $cat->value }}"
                                {{ old('category', $activity->category) == $cat->value ? 'selected' : '' }}>
                                {{ ucfirst($cat->value) }}
                            </option>
                        @endforeach
                    </select>

                    <x-input-label for="is_active" value="Is Active" />
                    <select id="is_active" name="is_active" class="block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="1" {{ old('is_active', $activity->is_active) == 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('is_active', $activity->is_active) == 0 ? 'selected' : '' }}>Inactive</option>
                    </select>

                    <x-input-label for="difficulty_level" value="Difficulty Level" />
                    <select id="difficulty_level" name="difficulty_level" class="block w-full border-gray-300 rounded-md shadow-sm">
                        @foreach(['easy','moderate','hard'] as $level)
                            <option value="{{ $level }}"
                                {{ old('difficulty_level', $activity->difficulty_level) == $level ? 'selected' : '' }}>
                                {{ ucfirst($level) }}
                            </option>
                        @endforeach
                    </select>

                    <x-input-label for="amenities" value="Amenities" />
                    <select name="amenities[]" id="amenities" class="block w-full border-gray-300 rounded-md shadow-sm" multiple>
                        @foreach(['WiFi', 'Parking', 'Pool', 'Restaurant', 'Bar'] as $amenity)
                            <option value="{{ $amenity }}"
                                {{ collect(old('amenities', $activity->amenities))->contains($amenity) ? 'selected' : '' }}>
                                {{ $amenity }}
                            </option>
                        @endforeach
                    </select>

                    <x-input-label for="address" value="Address" />
                    <x-text-input id="address" type="text" name="address" required
                        :value="old('address', $activity->address)" />
                        <x-input-label for="requires_booking" value="Requires Booking?" />
                    <div class="flex items-center space-x-4 mb-2">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" name="requires_booking" value="1"
                                {{ old('requires_booking', $activity->requires_booking) == 1 ? 'checked' : '' }} class="hidden">
                            <span class="px-3 py-1 {{ old('requires_booking', $activity->requires_booking) == 1 ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-800' }}">Yes</span>
                        </label>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" name="requires_booking" value="0"
                                {{ old('requires_booking', $activity->requires_booking) == 0 ? 'checked' : '' }} class="hidden">
                            <span class="px-3 py-1 {{ old('requires_booking', $activity->requires_booking) == 0 ? 'bg-red-500 text-white' : 'bg-gray-200 text-gray-800' }}">No</span>
                        </label>
                    </div>

                    <x-input-label for="pets_allowed" value="Pets Allowed?" />
                    <div class="flex items-center space-x-4 mb-2">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" name="pets_allowed" value="1"
                                {{ old('pets_allowed', $activity->pets_allowed) == 1 ? 'checked' : '' }} class="hidden">
                            <span class="px-3 py-1 {{ old('pets_allowed', $activity->pets_allowed) == 1 ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-800' }}">Yes</span>
                        </label>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" name="pets_allowed" value="0"
                                {{ old('pets_allowed', $activity->pets_allowed) == 0 ? 'checked' : '' }} class="hidden">
                            <span class="px-3 py-1 {{ old('pets_allowed', $activity->pets_allowed) == 0 ? 'bg-red-500 text-white' : 'bg-gray-200 text-gray-800' }}">No</span>
                        </label>
                    </div>

                    <x-input-label for="family_friendly" value="Family Friendly" />
                    <select id="family_friendly" name="family_friendly" class="block w-full border-gray-300 rounded-md shadow-sm" required>
                        @foreach(['all_ages', 'adults_only', 'families'] as $option)
                            <option value="{{ $option }}"
                                {{ old('family_friendly', $activity->family_friendly) == $option ? 'selected' : '' }}>
                                {{ ucwords(str_replace('_', ' ', $option)) }}
                            </option>
                        @endforeach
                    </select>

                    <x-input-label for="highlights" value="Highlights" />
                    <textarea id="highlights" name="highlights" class="block w-full border-gray-300 rounded-md shadow-sm">{{ old('highlights', $activity->highlights) }}</textarea>

                    <x-input-label for="image" value="Activity Image" />
                    <input type="file" id="image" name="image" accept="image/*">
                </div>
            </div>

            <div class="popup-buttons mt-4">
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('activities.index') }}" class="cancel-btn">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>
