<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/transport.css') }}">
        <link rel="stylesheet" href="{{ asset('css/vehicles.css') }}">
    @endpush

    <div class="vehicle-form-container">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Edit Driver Details</h2>

       
        <form action="{{ route('drivers.update', $driver->id) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PUT')  @if ($errors->any()) <div
                            class="mb-4 p-4 bg-red-100 border border-red-200 text-red-800 rounded-lg">
                            <p class="font-bold">Please fix the following errors:</p>
                            <ul class="list-disc list-inside mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                </div>
            @endif

    <div class="first-section">
        <div class="left">
            <x-input-label for="name" value="Full Name" />
         
            <x-text-input id="name" type="text" name="name" :value="old('name', $driver->user->name)" required />

            <x-input-label for="email" value="Email Address" />
            <x-text-input id="email" type="email" name="email" :value="old('email', $driver->user->email)" required />

            <x-input-label for="phone" value="Phone Number" />
            <x-text-input id="phone" type="text" name="phone" :value="old('phone', $driver->user->phone_number)" required />

            <x-input-label for="address" value="Address" />
            <x-text-input id="address" type="text" name="address" :value="old('address', $driver->address)" />

            <x-input-label for="age" value="Age" />
            <x-text-input id="age" type="number" name="age" :value="old('age', $driver->age)" />
        </div>

        <div class="right">
            <x-input-label for="date_of_hire" value="Date of Hire" />
            <x-text-input id="date_of_hire" type="date" name="date_of_hire" :value="old('date_of_hire', $driver->date_of_hire)" />


            <x-input-label for="license_category" value="License Category" />
            <select id="license_category" name="license_category"
                class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                <option value="A" @selected(old('license_category') == 'A')>Category A </option>
                <option value="B" @selected(old('license_category') == 'B')>Category B </option>
            </select>

                <x-input-label for="experience" value="Experience" />
                <textarea id="experience" name="experience"
                    class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                    rows="3">{{ old('experience', $driver->experience) }}</textarea>

                <x-input-label for="status" value="Status" />
                <select id="status" name="status"
                    class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    <option value="active" @selected(old('status', $driver->status) == 'active')>Available</option>
                    <option value="inactive" @selected(old('status', $driver->status) == 'inactive')>Unavailable</option>
                </select>
        </div>
    </div>

    <div class="mt-6">
        <x-input-label for="license_image" value="Update License Image (Optional)" />
        <div class="flex items-center mt-2">
            @if($driver->license_image)
                <img src="{{ asset('storage/' . $driver->license_image) }}" alt="License Image"
                    class="h-16 w-24 object-cover rounded-md mr-4 border">
            @endif
            <div>
                <input type="file" id="license_image" name="license_image" class="block w-full text-sm text-gray-500
                            file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0
                            file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700
                            hover:file:bg-blue-100">
                <p class="mt-1 text-xs text-gray-500">Leave blank to keep the current image.</p>
            </div>
        </div>
    </div>

    <div class="popup-buttons mt-8">
        <button type="submit" class="btn btn-primary">Update Driver</button>
        <a href="{{ route('drivers.index') }}" class="cancel-btn">Cancel</a>
    </div>
    </form>
    </div>
</x-app-layout>