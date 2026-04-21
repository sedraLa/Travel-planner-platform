<form method="POST" action="{{ $action }}" id="aiTripForm">
    @csrf
    @if(!empty($method) && strtoupper($method) !== 'POST')
        @method($method)
    @endif

    <div class="form-container">
        <h2 class="text-xl font-semibold mb-6"> Build from your DB catalog only</h2>

        @if ($errors->any())
            <div class="mb-4 px-4 py-3 bg-red-100 text-red-800 rounded">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="space-y-6">
            <div>
                <x-input-label for="destination_ids">Destinations from Database (Multi-select)</x-input-label>
                <select name="destination_ids[]" id="destination_ids" class="w-full rounded-md border-gray-300" multiple required size="6">
                    @foreach($destinations as $destination)
                        <option value="{{ $destination->id }}" @selected(in_array($destination->id, old('destination_ids', $formData['destination_ids'] ?? [])))>
                            {{ $destination->name }} - {{ $destination->city }}, {{ $destination->country }}
                        </option>
                    @endforeach
                </select>
                <p class="text-sm text-gray-500 mt-2">Use Ctrl/Cmd + click to select multiple destinations.</p>
            </div>

            <div>
                <x-input-label for="description">Trip Description</x-input-label>
                <textarea name="description" id="description" placeholder="e.g., relaxing family style with cultural activities"
                    class="w-full rounded-md border-gray-300 h-32">{{ old('description', $formData['description'] ?? null) }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <x-input-label for="max_participants">Max Participants</x-input-label>
                    <x-text-input type="number" name="max_participants" id="max_participants" min="1" value="{{ old('max_participants', $formData['max_participants'] ?? 1) }}" class="w-full"/>
                </div>
                <div>
                    <x-input-label for="duration">Duration (Days)</x-input-label>
                    <x-text-input type="number" name="duration" id="duration" min="1" value="{{ old('duration', $formData['duration'] ?? 3) }}" class="w-full"/>
                </div>
                <div>
                    <x-input-label for="budget">Estimated Budget ($)</x-input-label>
                    <x-text-input type="number" name="budget" id="budget" placeholder="Optional" value="{{ old('budget', $formData['budget'] ?? null) }}" class="w-full"/>
                </div>

                <div>
                    <x-input-label>Trip Categories (Multi-select)</x-input-label>
                    <div class="category-grid">
                        @foreach($categories as $category)
                            <label class="category-box">
                                <input type="checkbox" name="categories[]" value="{{ $category->value }}"
                                    {{ in_array($category->value, old('categories', $formData['categories'] ?? []), true) ? 'checked' : '' }}>
                                <span>{{ ucfirst($category->value) }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <x-input-label for="language">Language</x-input-label>
                    <select name="language" id="language" class="w-full rounded-md border-gray-300">
                        <option value="en" @selected(old('language', $formData['language'] ?? 'en') === 'en')>English</option>
                        <option value="ar" @selected(old('language', $formData['language'] ?? 'en') === 'ar')>Arabic</option>
                    </select>
                </div>
            </div>

            <div class="text-center pt-6">
                <button type="submit" class="generate-btn" id="generateBtn">
                    <span class="btn-text" style="color:white;">{{ $submitLabel }}</span>
                    <div class="spinner" id="btnSpinner"></div>
                </button>
            </div>
        </div>
    </div>
</form>
