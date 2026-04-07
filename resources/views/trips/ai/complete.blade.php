<x-app-layout>
    <div class="max-w-7xl mx-auto py-8 px-4">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-3xl font-bold">{{ $trip->name }}</h1>
                <p class="text-sm text-gray-500 mt-1">Trip completion workspace (save each tab separately).</p>
            </div>
            <a href="{{ route('trips.index') }}" class="px-4 py-2 border rounded-lg">All Trips</a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-3 rounded mb-4">{{ session('success') }}</div>
        @endif

        <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 p-3 rounded mb-4">
            ⚠️ Important: each tab has its own Save button. You must save before moving to another tab.
        </div>

        @php
            $tabs = [
                'basics' => 'Basics',
                'days' => 'Days & Activities',
                'packages' => 'Packages',
                'schedules' => 'Schedules',
                'images' => 'Images',
                'guides' => 'Guides',
                'transports' => 'Transport',
            ];
        @endphp

        <div class="flex flex-wrap gap-2 mb-6">
            @foreach($tabs as $key => $label)
                <a href="{{ route('trip.complete.edit', ['trip' => $trip->id, 'tab' => $key]) }}"
                   class="px-4 py-2 rounded-lg border {{ $activeTab === $key ? 'bg-blue-900 text-white' : 'bg-white text-gray-700' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        @if($activeTab === 'basics')
            <form method="POST" action="{{ route('trip.complete.basics', $trip->id) }}" class="bg-white border rounded-xl p-5 space-y-4">
                @csrf
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm">Name</label>
                        <input name="name" value="{{ old('name', $trip->name) }}" class="w-full border rounded p-2" required>
                    </div>
                    <div>
                        <label class="block text-sm">Primary destination</label>
                        <select name="destination_id" class="w-full border rounded p-2" required>
                            @foreach($destinations as $destination)
                                <option value="{{ $destination->id }}" @selected(old('destination_id', $trip->destination_id) == $destination->id)>{{ $destination->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm">Itinerary destinations</label>
                        @php
                            $selectedDestinationIds = array_map('intval', old(
                                'destination_ids',
                                $trip->itineraryDestinations->pluck('id')->all() ?: [$trip->destination_id]
                            ));
                            if (empty($selectedDestinationIds)) {
                                $selectedDestinationIds = [0];
                            }
                        @endphp
                        <div id="itinerary-destinations-repeater" class="space-y-2">
                            @foreach($selectedDestinationIds as $index => $selectedDestinationId)
                                <div class="itinerary-destination-row flex items-center gap-2">
                                    <select name="destination_ids[]" class="flex-1 border rounded p-2" required>
                                        <option value="">Select destination</option>
                                        @foreach($destinations as $destination)
                                            <option value="{{ $destination->id }}" @selected($destination->id === (int) $selectedDestinationId)>
                                                {{ $destination->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="button" class="remove-destination-row px-3 py-2 border rounded text-red-600">Remove</button>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" id="add-itinerary-destination-row" class="mt-2 px-3 py-2 border rounded bg-gray-50">
                            + Add destination
                        </button>
                        <p class="text-xs text-gray-500 mt-1">Admin can add/remove itinerary destinations. Primary destination will always be included when saving.</p>
                    </div>
                    <div>
                        <label class="block text-sm">Duration (days)</label>
                        <input type="number" name="duration_days" value="{{ old('duration_days', $trip->duration_days) }}" class="w-full border rounded p-2" min="1" required>
                    </div>
                    <div>
                        <label class="block text-sm">Max participants</label>
                        <input type="number" name="max_participants" value="{{ old('max_participants', $trip->max_participants) }}" class="w-full border rounded p-2" min="1">
                    </div>
                    <div>
                        <label class="block text-sm">Category</label>
                        <input name="category" value="{{ old('category', $trip->category) }}" class="w-full border rounded p-2">
                    </div>
                    <div>
                        <label class="block text-sm">Status</label>
                        <input value="Draft" class="w-full border rounded p-2 bg-gray-100 text-gray-600" disabled>
                        <p class="text-xs text-gray-500 mt-1">Status stays Draft here until final Publish step.</p>
                    </div>
                </div>
                <div>
                    <label class="block text-sm">Description</label>
                    <textarea name="description" rows="4" class="w-full border rounded p-2">{{ old('description', $trip->description) }}</textarea>
                </div>
                <div>
                    <label class="block text-sm">Meeting point description</label>
                    <textarea name="meeting_point_description" rows="2" class="w-full border rounded p-2">{{ old('meeting_point_description', $trip->meeting_point_description) }}</textarea>
                </div>
                <div>
                    <label class="block text-sm">Meeting point address</label>
                    <input name="meeting_point_address" value="{{ old('meeting_point_address', $trip->meeting_point_address) }}" class="w-full border rounded p-2">
                </div>
                <button class="px-5 py-2 bg-blue-600 text-white rounded">Save Basics</button>
            </form>
        @endif

        @if($activeTab === 'days')
            <form method="POST" action="{{ route('trip.complete.days', $trip->id) }}" class="space-y-4">
                @csrf
                @foreach($trip->days->sortBy('day_number') as $dayIndex => $day)
                    <div class="bg-white border rounded-xl p-4 space-y-3">
                        <h3 class="font-semibold">Day {{ $day->day_number }}</h3>
                        <input type="hidden" name="days[{{ $dayIndex }}][day_number]" value="{{ $day->day_number }}">
                        <div class="grid md:grid-cols-2 gap-3">
                            <div>
                                <label class="text-sm block">Title</label>
                                <input name="days[{{ $dayIndex }}][title]" value="{{ old("days.$dayIndex.title", $day->title) }}" class="w-full border rounded p-2">
                            </div>
                            <div>
                                <label class="text-sm block">Hotel</label>
                                <select name="days[{{ $dayIndex }}][hotel_id]" class="w-full border rounded p-2">
                                    <option value="">-</option>
                                    @foreach($hotels as $hotel)
                                        <option value="{{ $hotel->id }}" @selected(old("days.$dayIndex.hotel_id", $day->hotel_id) == $hotel->id)>{{ $hotel->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="text-sm block">Description</label>
                            <textarea name="days[{{ $dayIndex }}][description]" rows="2" class="w-full border rounded p-2">{{ old("days.$dayIndex.description", $day->description) }}</textarea>
                        </div>
                        <p class="text-xs text-gray-500">
                            Activity start/end time and notes are saved per day in <code>day_activities</code>, not in the master activities catalog.
                        </p>

                        @foreach($day->activities as $activityIndex => $activity)
                            <div class="grid md:grid-cols-4 gap-2 p-3 border rounded">
                                <input type="hidden" name="days[{{ $dayIndex }}][activities][{{ $activityIndex }}][id]" value="{{ $activity->id }}">
                                <div>
                                    <label class="text-xs block">Activity</label>
                                    <select name="days[{{ $dayIndex }}][activities][{{ $activityIndex }}][activity_id]" class="w-full border rounded p-2">
                                        <option value="">-</option>
                                        @foreach($activities as $activityOption)
                                            <option value="{{ $activityOption->id }}" @selected(old("days.$dayIndex.activities.$activityIndex.activity_id", $activity->activity_id) == $activityOption->id)>{{ $activityOption->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="text-xs block">Start</label>
                                    <input type="time" name="days[{{ $dayIndex }}][activities][{{ $activityIndex }}][start_time]" value="{{ old("days.$dayIndex.activities.$activityIndex.start_time", $activity->start_time ? \Carbon\Carbon::parse($activity->start_time)->format('H:i') : '') }}" class="w-full border rounded p-2">
                                </div>
                                <div>
                                    <label class="text-xs block">End</label>
                                    <input type="time" name="days[{{ $dayIndex }}][activities][{{ $activityIndex }}][end_time]" value="{{ old("days.$dayIndex.activities.$activityIndex.end_time", $activity->end_time ? \Carbon\Carbon::parse($activity->end_time)->format('H:i') : '') }}" class="w-full border rounded p-2">
                                </div>
                                <div>
                                    <label class="text-xs block">Notes</label>
                                    <input name="days[{{ $dayIndex }}][activities][{{ $activityIndex }}][notes]" value="{{ old("days.$dayIndex.activities.$activityIndex.notes", $activity->notes) }}" class="w-full border rounded p-2">
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
                <button class="px-5 py-2 bg-blue-600 text-white rounded">Save Days & Activities</button>
            </form>
        @endif

        @if($activeTab === 'packages')
            <form method="POST" action="{{ route('trip.complete.packages', $trip->id) }}" class="space-y-4">
                @csrf
                @php
                    $packages = old('packages', $trip->packages->map(function ($package) {
                        return [
                            'id' => $package->id,
                            'name' => $package->name,
                            'price' => $package->price,
                            'includes' => $package->includes->pluck('content')->values()->all(),
                            'excludes' => $package->excludes->pluck('content')->values()->all(),
                            'highlights' => $package->highlights->pluck('title')->values()->all(),
                            'hotels' => $package->packageHotels->map(function ($packageHotel) {
                                return [
                                    'hotel_id' => $packageHotel->hotel_id,
                                    'room_type' => $packageHotel->room_type,
                                    'meal_plan' => $packageHotel->meal_plan,
                                    'amenities' => is_array($packageHotel->amenities) ? implode(', ', $packageHotel->amenities) : '',
                                    'notes' => $packageHotel->notes,
                                    'hotel_name' => optional($packageHotel->hotel)->name,
                                ];
                            })->values()->all(),
                        ];
                    })->values()->all());

                    $canonicalHotels = $trip->days
                        ->whereNotNull('hotel_id')
                        ->map(fn ($day) => [
                            'hotel_id' => $day->hotel_id,
                            'hotel_name' => optional($day->hotel)->name,
                        ])
                        ->unique('hotel_id')
                        ->values()
                        ->all();
                @endphp

                <div id="packages-repeater" class="space-y-4">
                    @forelse($packages as $packageIndex => $package)
                        <div class="package-card bg-white border rounded-xl p-4 space-y-3" data-package-index="{{ $packageIndex }}">
                            <div class="flex justify-between items-start gap-3">
                                <h3 class="font-semibold text-gray-800">Package #<span class="package-seq">{{ $packageIndex + 1 }}</span></h3>
                                <button type="button" class="remove-package-btn px-3 py-2 border rounded text-red-600">Remove package</button>
                            </div>

                            <input type="hidden" name="packages[{{ $packageIndex }}][id]" value="{{ $package['id'] ?? '' }}">
                            <div class="grid md:grid-cols-2 gap-3">
                                <input name="packages[{{ $packageIndex }}][name]" value="{{ $package['name'] ?? '' }}" class="border rounded p-2" placeholder="Package name" required>
                                <input type="number" step="0.01" name="packages[{{ $packageIndex }}][price]" value="{{ $package['price'] ?? '' }}" class="border rounded p-2" placeholder="Price" required>
                            </div>

                            <div class="grid md:grid-cols-3 gap-3">
                                <div class="border rounded p-3 space-y-2">
                                    <div class="flex justify-between items-center">
                                        <label class="text-sm font-medium">Includes</label>
                                        <button type="button" class="add-item-btn text-xs px-2 py-1 border rounded" data-list-type="includes">+ Add</button>
                                    </div>
                                    <div class="items-list space-y-2" data-list-type="includes">
                                        @foreach(($package['includes'] ?? []) as $itemIndex => $include)
                                            <div class="flex gap-2">
                                                <input name="packages[{{ $packageIndex }}][includes][]" value="{{ $include }}" class="w-full border rounded p-2" placeholder="Include item">
                                                <button type="button" class="remove-item-btn px-2 border rounded text-red-600">×</button>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="border rounded p-3 space-y-2">
                                    <div class="flex justify-between items-center">
                                        <label class="text-sm font-medium">Excludes</label>
                                        <button type="button" class="add-item-btn text-xs px-2 py-1 border rounded" data-list-type="excludes">+ Add</button>
                                    </div>
                                    <div class="items-list space-y-2" data-list-type="excludes">
                                        @foreach(($package['excludes'] ?? []) as $itemIndex => $exclude)
                                            <div class="flex gap-2">
                                                <input name="packages[{{ $packageIndex }}][excludes][]" value="{{ $exclude }}" class="w-full border rounded p-2" placeholder="Exclude item">
                                                <button type="button" class="remove-item-btn px-2 border rounded text-red-600">×</button>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="border rounded p-3 space-y-2">
                                    <div class="flex justify-between items-center">
                                        <label class="text-sm font-medium">Highlights</label>
                                        <button type="button" class="add-item-btn text-xs px-2 py-1 border rounded" data-list-type="highlights">+ Add</button>
                                    </div>
                                    <div class="items-list space-y-2" data-list-type="highlights">
                                        @foreach(($package['highlights'] ?? []) as $itemIndex => $highlight)
                                            <div class="flex gap-2">
                                                <input name="packages[{{ $packageIndex }}][highlights][]" value="{{ $highlight }}" class="w-full border rounded p-2" placeholder="Highlight item">
                                                <button type="button" class="remove-item-btn px-2 border rounded text-red-600">×</button>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            @foreach(($package['hotels'] ?? []) as $hotelIndex => $packageHotel)
                                <div class="grid md:grid-cols-4 gap-2 p-2 border rounded">
                                    <input type="hidden" name="packages[{{ $packageIndex }}][hotels][{{ $hotelIndex }}][hotel_id]" value="{{ $packageHotel['hotel_id'] ?? '' }}">
                                    <input value="{{ $packageHotel['hotel_name'] ?? '' }}" class="border rounded p-2 bg-gray-100" readonly>
                                    <input name="packages[{{ $packageIndex }}][hotels][{{ $hotelIndex }}][room_type]" value="{{ $packageHotel['room_type'] ?? '' }}" class="border rounded p-2" placeholder="Room type">
                                    <input name="packages[{{ $packageIndex }}][hotels][{{ $hotelIndex }}][meal_plan]" value="{{ $packageHotel['meal_plan'] ?? '' }}" class="border rounded p-2" placeholder="Meal plan">
                                    <input name="packages[{{ $packageIndex }}][hotels][{{ $hotelIndex }}][amenities]" value="{{ $packageHotel['amenities'] ?? '' }}" class="border rounded p-2" placeholder="Amenities comma separated">
                                    <textarea name="packages[{{ $packageIndex }}][hotels][{{ $hotelIndex }}][notes]" class="border rounded p-2 md:col-span-4" rows="2" placeholder="Notes / package differentiation">{{ $packageHotel['notes'] ?? '' }}</textarea>
                                </div>
                            @endforeach
                        </div>
                    @empty
                        <div class="bg-white border rounded-xl p-4">
                            <p class="mb-2 text-sm text-gray-600">No packages yet. Click "+ Add package".</p>
                        </div>
                    @endforelse
                </div>

                <button type="button" id="add-package-btn" class="px-4 py-2 border rounded bg-gray-50">+ Add package</button>
                <button class="px-5 py-2 bg-blue-600 text-white rounded">Save Packages</button>
            </form>
        @endif

        @if($activeTab === 'schedules')
            <form method="POST" action="{{ route('trip.complete.schedules', $trip->id) }}" class="space-y-3">
                @csrf
                @php
                    $schedules = old('schedules', $trip->schedules->map(fn ($schedule) => [
                        'id' => $schedule->id,
                        'start_date' => $schedule->start_date,
                        'end_date' => $schedule->end_date,
                        'booking_deadline' => $schedule->booking_deadline,
                        'available_seats' => $schedule->available_seats,
                        'price_modifier' => $schedule->price_modifier,
                        'status' => $schedule->status,
                    ])->values()->all());
                @endphp
                <div id="schedules-repeater" class="space-y-3">
                    @forelse($schedules as $index => $schedule)
                        <div class="schedule-row bg-white border rounded-xl p-3 space-y-2" data-schedule-index="{{ $index }}">
                            <div class="flex justify-between">
                                <h3 class="font-semibold text-sm text-gray-700">Schedule #<span class="schedule-seq">{{ $index + 1 }}</span></h3>
                                <button type="button" class="remove-schedule-btn px-2 py-1 border rounded text-red-600">Remove</button>
                            </div>
                            <input type="hidden" name="schedules[{{ $index }}][id]" value="{{ $schedule['id'] ?? '' }}">
                            <div class="grid md:grid-cols-6 gap-2">
                                <input type="date" name="schedules[{{ $index }}][start_date]" value="{{ $schedule['start_date'] ?? '' }}" class="border rounded p-2" required>
                                <input type="date" name="schedules[{{ $index }}][end_date]" value="{{ $schedule['end_date'] ?? '' }}" class="border rounded p-2" required>
                                <input type="date" name="schedules[{{ $index }}][booking_deadline]" value="{{ $schedule['booking_deadline'] ?? '' }}" class="border rounded p-2">
                                <input type="number" name="schedules[{{ $index }}][available_seats]" value="{{ $schedule['available_seats'] ?? '' }}" class="border rounded p-2" placeholder="Seats">
                                <input type="number" step="0.01" name="schedules[{{ $index }}][price_modifier]" value="{{ $schedule['price_modifier'] ?? '' }}" class="border rounded p-2" placeholder="Price modifier">
                                <select name="schedules[{{ $index }}][status]" class="border rounded p-2">
                                    <option value="available" @selected(($schedule['status'] ?? 'available')==='available')>Available</option>
                                    <option value="full" @selected(($schedule['status'] ?? '')==='full')>Full</option>
                                    <option value="cancelled" @selected(($schedule['status'] ?? '')==='cancelled')>Cancelled</option>
                                </select>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white border rounded-xl p-4 text-sm text-gray-600">
                            No schedules yet. Click "+ Add schedule".
                        </div>
                    @endforelse
                </div>
                <button type="button" id="add-schedule-btn" class="px-4 py-2 border rounded bg-gray-50">+ Add schedule</button>
                <button class="px-5 py-2 bg-blue-600 text-white rounded">Save Schedules</button>
            </form>
        @endif

        @if($activeTab === 'images')
            <form method="POST" action="{{ route('trip.complete.images', $trip->id) }}" enctype="multipart/form-data" class="space-y-3 bg-white border rounded-xl p-4">
                @csrf
                <div>
                    <label class="block text-sm">Cover image path</label>
                    <input name="cover_image_path" value="{{ old('cover_image_path', optional($trip->images->firstWhere('is_cover', true))->image_path) }}" class="w-full border rounded p-2" placeholder="/storage/trips/cover.jpg">
                    <label class="block text-sm mt-2">Or choose cover image</label>
                    <input type="file" name="cover_image_file" accept="image/*" class="w-full border rounded p-2">
                </div>

                @php
                    $otherImages = old('images', $trip->images->where('is_cover', false)->values()->map(fn ($image) => [
                        'id' => $image->id,
                        'image_path' => $image->image_path,
                    ])->all());
                @endphp
                <div class="flex justify-between items-center">
                    <label class="block text-sm">Gallery images</label>
                    <button type="button" id="add-image-btn" class="px-3 py-1 border rounded text-sm">+ Add image</button>
                </div>
                <div id="images-repeater" class="space-y-2">
                    @forelse($otherImages as $index => $image)
                        <div class="image-row flex items-center gap-2" data-image-index="{{ $index }}">
                            <input type="hidden" name="images[{{ $index }}][id]" value="{{ $image['id'] ?? '' }}">
                            <input type="hidden" name="images[{{ $index }}][existing_path]" value="{{ $image['image_path'] ?? '' }}">
                            <input name="images[{{ $index }}][image_path]" value="{{ $image['image_path'] ?? '' }}" class="w-full border rounded p-2" placeholder="Other image path">
                            <input type="file" name="images[{{ $index }}][image_file]" accept="image/*" class="w-full border rounded p-2">
                            <button type="button" class="remove-image-btn px-3 py-2 border rounded text-red-600">Remove</button>
                        </div>
                    @empty
                        <p class="text-sm text-gray-600" id="images-empty-note">No images yet. Click "+ Add image".</p>
                    @endforelse
                </div>
                <button class="px-5 py-2 bg-blue-600 text-white rounded">Save Images</button>
            </form>
        @endif

        @if($activeTab === 'guides')
            <div class="bg-white border rounded-xl p-6 text-gray-600">Guides form is intentionally left empty for now.</div>
        @endif

        @if($activeTab === 'transports')
            <div class="bg-white border rounded-xl p-6 text-gray-600">Transports form is intentionally left empty for now.</div>
        @endif
    </div>

    @if($activeTab === 'basics')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const repeater = document.getElementById('itinerary-destinations-repeater');
                const addButton = document.getElementById('add-itinerary-destination-row');

                if (!repeater || !addButton) return;

                const destinationOptions = @json($destinations->map(fn ($destination) => [
                    'id' => $destination->id,
                    'name' => $destination->name,
                ])->values());

                const buildRow = () => {
                    const row = document.createElement('div');
                    row.className = 'itinerary-destination-row flex items-center gap-2';

                    const select = document.createElement('select');
                    select.name = 'destination_ids[]';
                    select.required = true;
                    select.className = 'flex-1 border rounded p-2';

                    const placeholderOption = document.createElement('option');
                    placeholderOption.value = '';
                    placeholderOption.textContent = 'Select destination';
                    select.appendChild(placeholderOption);

                    destinationOptions.forEach((destination) => {
                        const option = document.createElement('option');
                        option.value = destination.id;
                        option.textContent = destination.name;
                        select.appendChild(option);
                    });

                    const removeButton = document.createElement('button');
                    removeButton.type = 'button';
                    removeButton.className = 'remove-destination-row px-3 py-2 border rounded text-red-600';
                    removeButton.textContent = 'Remove';

                    row.appendChild(select);
                    row.appendChild(removeButton);

                    return row;
                };

                addButton.addEventListener('click', function () {
                    repeater.appendChild(buildRow());
                });

                repeater.addEventListener('click', function (event) {
                    const removeButton = event.target.closest('.remove-destination-row');

                    if (!removeButton) return;

                    const row = removeButton.closest('.itinerary-destination-row');
                    if (!row) return;

                    if (repeater.querySelectorAll('.itinerary-destination-row').length === 1) {
                        row.querySelector('select').value = '';
                        return;
                    }

                    row.remove();
                });
            });
        </script>
    @endif

    @if($activeTab === 'packages')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const repeater = document.getElementById('packages-repeater');
                const addPackageBtn = document.getElementById('add-package-btn');
                if (!repeater || !addPackageBtn) return;
                const canonicalHotels = @json($canonicalHotels);

                const renumberPackages = () => {
                    repeater.querySelectorAll('.package-card').forEach((card, packageIndex) => {
                        card.dataset.packageIndex = packageIndex;
                        const seq = card.querySelector('.package-seq');
                        if (seq) seq.textContent = packageIndex + 1;

                        card.querySelectorAll('input, textarea, select').forEach((field) => {
                            if (!field.name) return;
                            field.name = field.name.replace(/packages\[\d+]/, `packages[${packageIndex}]`);
                        });
                    });
                };

                const itemRow = (packageIndex, listType) => `
                    <div class="flex gap-2">
                        <input name="packages[${packageIndex}][${listType}][]" class="w-full border rounded p-2" placeholder="${listType.slice(0, -1)} item">
                        <button type="button" class="remove-item-btn px-2 border rounded text-red-600">×</button>
                    </div>
                `;

                const packageTemplate = () => {
                    const nextIndex = repeater.querySelectorAll('.package-card').length;
                    const hotelsRows = canonicalHotels.map((hotel, hotelIndex) => `
                        <div class="grid md:grid-cols-4 gap-2 p-2 border rounded">
                            <input type="hidden" name="packages[${nextIndex}][hotels][${hotelIndex}][hotel_id]" value="${hotel.hotel_id}">
                            <input value="${hotel.hotel_name ?? ''}" class="border rounded p-2 bg-gray-100" readonly>
                            <input name="packages[${nextIndex}][hotels][${hotelIndex}][room_type]" class="border rounded p-2" placeholder="Room type">
                            <input name="packages[${nextIndex}][hotels][${hotelIndex}][meal_plan]" class="border rounded p-2" placeholder="Meal plan">
                            <input name="packages[${nextIndex}][hotels][${hotelIndex}][amenities]" class="border rounded p-2" placeholder="Amenities comma separated">
                            <textarea name="packages[${nextIndex}][hotels][${hotelIndex}][notes]" class="border rounded p-2 md:col-span-4" rows="2" placeholder="Notes / package differentiation"></textarea>
                        </div>
                    `).join('');

                    return `
                        <div class="package-card bg-white border rounded-xl p-4 space-y-3" data-package-index="${nextIndex}">
                            <div class="flex justify-between items-start gap-3">
                                <h3 class="font-semibold text-gray-800">Package #<span class="package-seq">${nextIndex + 1}</span></h3>
                                <button type="button" class="remove-package-btn px-3 py-2 border rounded text-red-600">Remove package</button>
                            </div>
                            <input type="hidden" name="packages[${nextIndex}][id]" value="">
                            <div class="grid md:grid-cols-2 gap-3">
                                <input name="packages[${nextIndex}][name]" class="border rounded p-2" placeholder="Package name" required>
                                <input type="number" step="0.01" name="packages[${nextIndex}][price]" class="border rounded p-2" placeholder="Price" required>
                            </div>
                            <div class="grid md:grid-cols-3 gap-3">
                                <div class="border rounded p-3 space-y-2">
                                    <div class="flex justify-between items-center">
                                        <label class="text-sm font-medium">Includes</label>
                                        <button type="button" class="add-item-btn text-xs px-2 py-1 border rounded" data-list-type="includes">+ Add</button>
                                    </div>
                                    <div class="items-list space-y-2" data-list-type="includes"></div>
                                </div>
                                <div class="border rounded p-3 space-y-2">
                                    <div class="flex justify-between items-center">
                                        <label class="text-sm font-medium">Excludes</label>
                                        <button type="button" class="add-item-btn text-xs px-2 py-1 border rounded" data-list-type="excludes">+ Add</button>
                                    </div>
                                    <div class="items-list space-y-2" data-list-type="excludes"></div>
                                </div>
                                <div class="border rounded p-3 space-y-2">
                                    <div class="flex justify-between items-center">
                                        <label class="text-sm font-medium">Highlights</label>
                                        <button type="button" class="add-item-btn text-xs px-2 py-1 border rounded" data-list-type="highlights">+ Add</button>
                                    </div>
                                    <div class="items-list space-y-2" data-list-type="highlights"></div>
                                </div>
                            </div>
                            <div class="space-y-2">
                                ${hotelsRows || '<p class="text-sm text-gray-500">No hotels found from itinerary days yet.</p>'}
                            </div>
                        </div>
                    `;
                };

                addPackageBtn.addEventListener('click', function () {
                    repeater.insertAdjacentHTML('beforeend', packageTemplate());
                });

                repeater.addEventListener('click', function (event) {
                    const addItemButton = event.target.closest('.add-item-btn');
                    if (addItemButton) {
                        const packageCard = addItemButton.closest('.package-card');
                        const listType = addItemButton.dataset.listType;
                        const list = packageCard.querySelector(`.items-list[data-list-type="${listType}"]`);
                        list.insertAdjacentHTML('beforeend', itemRow(packageCard.dataset.packageIndex, listType));
                        return;
                    }

                    const removeItemButton = event.target.closest('.remove-item-btn');
                    if (removeItemButton) {
                        removeItemButton.closest('.flex').remove();
                        return;
                    }

                    const removePackageButton = event.target.closest('.remove-package-btn');
                    if (removePackageButton) {
                        removePackageButton.closest('.package-card').remove();
                        renumberPackages();
                    }
                });
            });
        </script>
    @endif

    @if($activeTab === 'schedules')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const repeater = document.getElementById('schedules-repeater');
                const addBtn = document.getElementById('add-schedule-btn');
                if (!repeater || !addBtn) return;

                const renumber = () => {
                    repeater.querySelectorAll('.schedule-row').forEach((row, index) => {
                        row.dataset.scheduleIndex = index;
                        row.querySelector('.schedule-seq').textContent = index + 1;
                        row.querySelectorAll('input, select').forEach((field) => {
                            if (!field.name) return;
                            field.name = field.name.replace(/schedules\[\d+]/, `schedules[${index}]`);
                        });
                    });
                };

                addBtn.addEventListener('click', function () {
                    const index = repeater.querySelectorAll('.schedule-row').length;
                    repeater.insertAdjacentHTML('beforeend', `
                        <div class="schedule-row bg-white border rounded-xl p-3 space-y-2" data-schedule-index="${index}">
                            <div class="flex justify-between">
                                <h3 class="font-semibold text-sm text-gray-700">Schedule #<span class="schedule-seq">${index + 1}</span></h3>
                                <button type="button" class="remove-schedule-btn px-2 py-1 border rounded text-red-600">Remove</button>
                            </div>
                            <input type="hidden" name="schedules[${index}][id]" value="">
                            <div class="grid md:grid-cols-6 gap-2">
                                <input type="date" name="schedules[${index}][start_date]" class="border rounded p-2" required>
                                <input type="date" name="schedules[${index}][end_date]" class="border rounded p-2" required>
                                <input type="date" name="schedules[${index}][booking_deadline]" class="border rounded p-2">
                                <input type="number" name="schedules[${index}][available_seats]" class="border rounded p-2" placeholder="Seats">
                                <input type="number" step="0.01" name="schedules[${index}][price_modifier]" class="border rounded p-2" placeholder="Price modifier">
                                <select name="schedules[${index}][status]" class="border rounded p-2">
                                    <option value="available">Available</option>
                                    <option value="full">Full</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                        </div>
                    `);
                });

                repeater.addEventListener('click', function (event) {
                    const removeBtn = event.target.closest('.remove-schedule-btn');
                    if (!removeBtn) return;
                    removeBtn.closest('.schedule-row').remove();
                    renumber();
                });
            });
        </script>
    @endif

    @if($activeTab === 'images')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const repeater = document.getElementById('images-repeater');
                const addBtn = document.getElementById('add-image-btn');
                if (!repeater || !addBtn) return;

                const emptyNote = document.getElementById('images-empty-note');

                const renumber = () => {
                    repeater.querySelectorAll('.image-row').forEach((row, index) => {
                        row.dataset.imageIndex = index;
                        row.querySelectorAll('input').forEach((field) => {
                            if (!field.name) return;
                            field.name = field.name.replace(/images\[\d+]/, `images[${index}]`);
                        });
                    });
                };

                addBtn.addEventListener('click', function () {
                    if (emptyNote) emptyNote.remove();
                    const index = repeater.querySelectorAll('.image-row').length;
                    repeater.insertAdjacentHTML('beforeend', `
                        <div class="image-row flex items-center gap-2" data-image-index="${index}">
                            <input type="hidden" name="images[${index}][id]" value="">
                            <input type="hidden" name="images[${index}][existing_path]" value="">
                            <input name="images[${index}][image_path]" class="w-full border rounded p-2" placeholder="Other image path">
                            <input type="file" name="images[${index}][image_file]" accept="image/*" class="w-full border rounded p-2">
                            <button type="button" class="remove-image-btn px-3 py-2 border rounded text-red-600">Remove</button>
                        </div>
                    `);
                });

                repeater.addEventListener('click', function (event) {
                    const removeBtn = event.target.closest('.remove-image-btn');
                    if (!removeBtn) return;
                    removeBtn.closest('.image-row').remove();
                    renumber();
                });
            });
        </script>
    @endif
</x-app-layout>
