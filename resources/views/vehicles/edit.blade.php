<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/transport.css') }}">
        <link rel="stylesheet" href="{{ asset('css/vehicles.css') }}">
    @endpush

    <div class="vehicle-form-container">

        <h2>Edit Vehicle</h2>


        <form action="{{ route('admin.vehicles.update', $vehicle->id) }}" method="post" enctype="multipart/form-data">
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


            <input type="hidden" name="transport_id" value="{{ $vehicle->transport_id }}">

            <div class="first-section">
    <div class="left">

        <x-input-label for="car_model" value="Car Model" />
        <x-text-input id="car_model" type="text" name="car_model" required
            placeholder="Enter car model"
            :value="old('car_model', $vehicle->car_model)" />

        <x-input-label for="plate_number" value="Plate Number" />
        <x-text-input id="plate_number" type="text" name="plate_number" required
            placeholder="Enter plate number"
            :value="old('plate_number', $vehicle->plate_number)" />

            <x-input-label for="driver_id" value="Select Driver" />
            <select id="driver_id" name="driver_id"
                class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                <option value="">-- Choose a driver --</option>
                @foreach ($drivers as $driver)
                    <option value="{{ $driver->id }}"
                        {{ old('driver_id', $vehicle->driver_id) == $driver->id ? 'selected' : '' }}>
                        {{ $driver->user->name }}
                    </option>
                @endforeach
            </select>

    </div>

    <div class="right">

        <x-input-label for="max_passengers" value="Max Passengers" />
        <x-text-input id="max_passengers" type="number" name="max_passengers"
            required placeholder="e.g. 4"
            :value="old('max_passengers', $vehicle->max_passengers)" />

        <x-input-label for="base_price" value="Base Price" />
        <x-text-input id="base_price" type="number" step="0.01" name="base_price" required
            placeholder="e.g. 50.00" :value="old('base_price', $vehicle->base_price)" />

        <x-input-label for="price_per_km" value="Price per KM" />
        <x-text-input id="price_per_km" type="number" step="0.01" name="price_per_km" required
            placeholder="e.g. 5.00" :value="old('price_per_km', $vehicle->price_per_km)" />

            <x-input-label for="category" value="Category" />

            <select id="category" name="category"
                class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">

                <option value="">-- Choose a category 'optional' --</option>

                <option value="luxury"  {{ old('category', $vehicle->category ?? '') == 'luxury' ? 'selected' : '' }}>LUXURY</option>
                <option value="standard" {{ old('category', $vehicle->category ?? '') == 'standard' ? 'selected' : '' }}>STANDARD</option>
                <option value="premium" {{ old('category', $vehicle->category ?? '') == 'premium' ? 'selected' : '' }}>PREMIUM</option>

                </option>

            </select>
    </div>
</div>


            <!-- Vehicle Image -->
            <x-input-label for="image" value="Vehicle Image" />
            <div style="margin-top: 8px;">
                @if($vehicle->image)
                    <div style="margin-bottom: 10px;">
                        <p style="font-size: 14px; color: #555;">Current Image:</p>
                        <img src="{{ asset('storage/' . $vehicle->image) }}" alt="Current Vehicle Image"
                            style="max-width: 150px; height: auto; border-radius: 8px; margin-top: 5px;">
                    </div>
                @endif
                <p style="font-size: 12px; color: #777;">Leave the file input empty to keep the current image.</p>
                <input type="file" id="image" name="image" accept="image/*">
            </div>
            <div class="popup-buttons">

                <button type="submit" class="btn btn-primary">Update Vehicle</button>
                <a href="{{route('transport.index')}}" class="cancel-btn">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>
