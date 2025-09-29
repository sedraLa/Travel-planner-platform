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

            <input type="hidden" name="transport_id" value="{{ $transportId }}">

            <div class="first-section">
                <div class="left">
                    <x-input-label for="car_model" value="Car Model" />
                    <x-text-input id="car_model" type="text" name="car_model" :value="old('car_model')" required
                        placeholder="Enter car model" />

                    <x-input-label for="plate_number" value="Plate Number" />
                    <x-text-input id="plate_number" type="text" name="plate_number" :value="old('plate_number')"
                        required placeholder="Enter plate number" />

                    

                    <x-input-label for="driver_id" value="Select Driver" />
                    <select id="driver_id" name="driver_id"
                        class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">-- Choose a driver --</option>
                        @foreach ($drivers as $driver)
                            <option value="{{ $driver->id }}" data-name="{{ $driver->name }}"
                                data-phone="{{ $driver->phone }}">
                                {{ $driver->name }}
                            </option>
                        @endforeach
                    </select>

                    
                    <input type="hidden" id="driver_name" name="driver_name">
                    <input type="hidden" id="driver_contact" name="driver_contact">

                    {{-- ===== نهاية التعديل ===== --}}

                </div>

                <div class="right">
                    <x-input-label for="max_passengers" value="Max Passengers" />
                    <x-text-input id="max_passengers" type="number" name="max_passengers" :value="old('max_passengers')"
                        required placeholder="e.g. 4" />

                    <x-input-label for="base_price" value="Base Price" />
                    <x-text-input id="base_price" type="number" step="0.01" name="base_price" :value="old('base_price')"
                        required placeholder="e.g. 50.00" />

                    <x-input-label for="price_per_km" value="Price per KM" />
                    <x-text-input id="price_per_km" type="number" step="0.01" name="price_per_km"
                        :value="old('price_per_km')" required placeholder="e.g. 5.00" />

                    <x-input-label for="category" value="Category" />
                    <x-text-input id="category" type="text" name="category" :value="old('category')"
                        placeholder="Optional" />
                </div>
            </div>

            <x-input-label for="image" value="Vehicle Image" />
            <input type="file" id="image" name="image" accept="image/*">

            <div class="popup-buttons">
                <button type="submit" class="btn btn-primary">Save</button>
                <a href="{{route('transport.index')}}" class="cancel-btn">Cancel</a>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const driverSelect = document.getElementById('driver_id');
                const driverNameInput = document.getElementById('driver_name');
                const driverContactInput = document.getElementById('driver_contact');

                driverSelect.addEventListener('change', function () {
                    // ابحث عن الخيار الذي تم تحديده
                    const selectedOption = this.options[this.selectedIndex];

                    if (selectedOption.value) {
                        // احصل على البيانات من data attributes
                        const name = selectedOption.getAttribute('data-name');
                        const phone = selectedOption.getAttribute('data-phone');

                        // قم بتعبئة الحقول المخفية بالبيانات
                        driverNameInput.value = name;
                        driverContactInput.value = phone;
                    } else {
                        // أفرغ الحقول إذا لم يتم اختيار أي سائق
                        driverNameInput.value = '';
                        driverContactInput.value = '';
                    }
                });
            });
        </script>
    @endpush
   

</x-app-layout>