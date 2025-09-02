<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/transport.css') }}">
        <link rel="stylesheet" href="{{ asset('css/vehicles.css') }}">
    @endpush

    <div class="vehicle-form-container">
        <h2>Add New Vehicle</h2>


        <form action="{{ route('vehicle.store') }}" method="post" enctype="multipart/form-data">
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

            <!-- Transport -->
            <input type="hidden" name="transport_id" value="{{ $transportId }}">


            <div class="first-section">
                <div class="left">
                    <x-input-label for="car_model" value="Car Model" />
                    <x-text-input id="car_model" type="text" name="car_model" required placeholder="Enter car model" />

                    <x-input-label for="plate_number" value="Plate Number" />
                    <x-text-input id="plate_number" type="text" name="plate_number" required
                        placeholder="Enter plate number" />

                    <x-input-label for="driver_name" value="Driver Name" />
                    <x-text-input id="driver_name" type="text" name="driver_name" required
                        placeholder="Enter driver name" />

                    <x-input-label for="driver_contact" value="Driver Contact" />
                    <x-text-input id="driver_contact" type="text" name="driver_contact" required
                        placeholder="Enter driver contact" />
                </div>

                <div class="right">
                    <x-input-label for="max_passengers" value="Max Passengers" />
                    <x-text-input id="max_passengers" type="number" name="max_passengers" required
                        placeholder="e.g. 4" />

                    <x-input-label for="base_price" value="Base Price" />
                    <x-text-input id="base_price" type="number" step="0.01" name="base_price" required
                        placeholder="e.g. 50.00" />

                    <x-input-label for="price_per_km" value="Price per KM" />
                    <x-text-input id="price_per_km" type="number" step="0.01" name="price_per_km" required
                        placeholder="e.g. 5.00" />

                    <x-input-label for="category" value="Category" />
                    <x-text-input id="category" type="text" name="category" placeholder="Optional" />
                </div>
            </div>

            <!-- Vehicle Image -->
            <x-input-label for="image" value="Vehicle Image" />
            <input type="file" id="image" name="image" accept="image/*">

            <div class="popup-buttons">
                <button type="submit" class="btn btn-primary">Save</button>
                <a href="{{route('transport.index')}}" class="cancel-btn">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>