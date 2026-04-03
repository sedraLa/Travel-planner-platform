<x-app-layout>
    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/cardetails.css') }}">
    @endpush

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
                        <select name="status" class="w-full border rounded p-2">
                            <option value="draft" @selected(old('status', $trip->status)==='draft')>Draft</option>
                            <option value="published" @selected(old('status', $trip->status)==='published')>Published</option>
                        </select>
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
                @forelse($trip->packages as $packageIndex => $package)
                    <div class="bg-white border rounded-xl p-4 space-y-3">
                        <input type="hidden" name="packages[{{ $packageIndex }}][id]" value="{{ $package->id }}">
                        <div class="grid md:grid-cols-2 gap-3">
                            <input name="packages[{{ $packageIndex }}][name]" value="{{ old("packages.$packageIndex.name", $package->name) }}" class="border rounded p-2" placeholder="Package name" required>
                            <input type="number" step="0.01" name="packages[{{ $packageIndex }}][price]" value="{{ old("packages.$packageIndex.price", $package->price) }}" class="border rounded p-2" placeholder="Price" required>
                        </div>
                        <textarea name="packages[{{ $packageIndex }}][includes]" rows="3" class="w-full border rounded p-2" placeholder="Includes (one item per line)">{{ old("packages.$packageIndex.includes", $package->includes->pluck('content')->implode("\n")) }}</textarea>
                        <textarea name="packages[{{ $packageIndex }}][excludes]" rows="3" class="w-full border rounded p-2" placeholder="Excludes (one item per line)">{{ old("packages.$packageIndex.excludes", $package->excludes->pluck('content')->implode("\n")) }}</textarea>
                        <textarea name="packages[{{ $packageIndex }}][highlights]" rows="3" class="w-full border rounded p-2" placeholder="Highlights (one line per highlight)">{{ old("packages.$packageIndex.highlights", $package->highlights->pluck('title')->implode("\n")) }}</textarea>

                        @foreach($package->packageHotels as $hotelIndex => $packageHotel)
                            <div class="grid md:grid-cols-4 gap-2 p-2 border rounded">
                                <select name="packages[{{ $packageIndex }}][hotels][{{ $hotelIndex }}][hotel_id]" class="border rounded p-2">
                                    <option value="">Hotel</option>
                                    @foreach($hotels as $hotel)
                                        <option value="{{ $hotel->id }}" @selected(old("packages.$packageIndex.hotels.$hotelIndex.hotel_id", $packageHotel->hotel_id) == $hotel->id)>{{ $hotel->name }}</option>
                                    @endforeach
                                </select>
                                <input name="packages[{{ $packageIndex }}][hotels][{{ $hotelIndex }}][room_type]" value="{{ old("packages.$packageIndex.hotels.$hotelIndex.room_type", $packageHotel->room_type) }}" class="border rounded p-2" placeholder="Room type">
                                <input name="packages[{{ $packageIndex }}][hotels][{{ $hotelIndex }}][meal_plan]" value="{{ old("packages.$packageIndex.hotels.$hotelIndex.meal_plan", $packageHotel->meal_plan) }}" class="border rounded p-2" placeholder="Meal plan">
                                <input name="packages[{{ $packageIndex }}][hotels][{{ $hotelIndex }}][amenities]" value="{{ old("packages.$packageIndex.hotels.$hotelIndex.amenities", is_array($packageHotel->amenities) ? implode(', ', $packageHotel->amenities) : '') }}" class="border rounded p-2" placeholder="Amenities comma separated">
                            </div>
                        @endforeach
                    </div>
                @empty
                    <div class="bg-white border rounded-xl p-4">
                        <p class="mb-3">No packages yet. Add one package card manually below:</p>
                        <input name="packages[0][name]" class="border rounded p-2 w-full mb-2" placeholder="Package name" required>
                        <input type="number" step="0.01" name="packages[0][price]" class="border rounded p-2 w-full" placeholder="Price" required>
                    </div>
                @endforelse

                @php $newIndex = $trip->packages->count(); @endphp
                <div class="bg-white border border-dashed rounded-xl p-4 space-y-2">
                    <p class="text-sm text-gray-600">Add package (optional)</p>
                    <input name="packages[{{ $newIndex }}][name]" class="border rounded p-2 w-full" placeholder="New package name">
                    <input type="number" step="0.01" name="packages[{{ $newIndex }}][price]" class="border rounded p-2 w-full" placeholder="New package price">
                    <textarea name="packages[{{ $newIndex }}][includes]" rows="2" class="w-full border rounded p-2" placeholder="Includes (one per line)"></textarea>
                </div>
                <button class="px-5 py-2 bg-blue-600 text-white rounded">Save Packages</button>
            </form>
        @endif

        @if($activeTab === 'schedules')
            <form method="POST" action="{{ route('trip.complete.schedules', $trip->id) }}" class="space-y-3">
                @csrf
                @forelse($trip->schedules as $index => $schedule)
                    <div class="grid md:grid-cols-6 gap-2 bg-white border rounded-xl p-3">
                        <input type="hidden" name="schedules[{{ $index }}][id]" value="{{ $schedule->id }}">
                        <input type="date" name="schedules[{{ $index }}][start_date]" value="{{ old("schedules.$index.start_date", $schedule->start_date) }}" class="border rounded p-2" required>
                        <input type="date" name="schedules[{{ $index }}][end_date]" value="{{ old("schedules.$index.end_date", $schedule->end_date) }}" class="border rounded p-2" required>
                        <input type="date" name="schedules[{{ $index }}][booking_deadline]" value="{{ old("schedules.$index.booking_deadline", $schedule->booking_deadline) }}" class="border rounded p-2">
                        <input type="number" name="schedules[{{ $index }}][available_seats]" value="{{ old("schedules.$index.available_seats", $schedule->available_seats) }}" class="border rounded p-2" placeholder="Seats">
                        <input type="number" step="0.01" name="schedules[{{ $index }}][price_modifier]" value="{{ old("schedules.$index.price_modifier", $schedule->price_modifier) }}" class="border rounded p-2" placeholder="Price modifier">
                        <select name="schedules[{{ $index }}][status]" class="border rounded p-2">
                            <option value="available" @selected(old("schedules.$index.status", $schedule->status)==='available')>Available</option>
                            <option value="full" @selected(old("schedules.$index.status", $schedule->status)==='full')>Full</option>
                            <option value="cancelled" @selected(old("schedules.$index.status", $schedule->status)==='cancelled')>Cancelled</option>
                        </select>
                    </div>
                @empty
                    <div class="bg-white border rounded-xl p-4 grid md:grid-cols-3 gap-2">
                        <input type="date" name="schedules[0][start_date]" class="border rounded p-2" required>
                        <input type="date" name="schedules[0][end_date]" class="border rounded p-2" required>
                        <select name="schedules[0][status]" class="border rounded p-2">
                            <option value="available">Available</option>
                            <option value="full">Full</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                @endforelse
                <button class="px-5 py-2 bg-blue-600 text-white rounded">Save Schedules</button>
            </form>
        @endif

        @if($activeTab === 'images')
            <form method="POST" action="{{ route('trip.complete.images', $trip->id) }}" class="space-y-3 bg-white border rounded-xl p-4">
                @csrf
                <div>
                    <label class="block text-sm">Cover image path</label>
                    <input name="cover_image_path" value="{{ old('cover_image_path', optional($trip->images->firstWhere('is_cover', true))->image_path) }}" class="w-full border rounded p-2" placeholder="/storage/trips/cover.jpg">
                </div>

                @php $otherImages = $trip->images->where('is_cover', false)->values(); @endphp
                @for($i = 0; $i < max(3, $otherImages->count()); $i++)
                    <input name="images[{{ $i }}][image_path]" value="{{ old("images.$i.image_path", optional($otherImages->get($i))->image_path) }}" class="w-full border rounded p-2" placeholder="Other image path">
@endfor
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
</x-app-layout>
