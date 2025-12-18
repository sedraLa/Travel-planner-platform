<!--<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/transport.css') }}"> 
        <link rel="stylesheet" href="{{ asset('css/vehicles.css') }}">
    @endpush

    <div class="vehicle-form-container"> 
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Add New Driver</h2>

        <form action="{{ route('drivers.store') }}" method="post" enctype="multipart/form-data">
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
                    <x-input-label for="name" value="First Name" />
                    <x-text-input id="name" type="text" name="name" :value="old('name')" required
                        placeholder="Enter driver's full name" />

                        <x-input-label for="last_name" value="Last name" />
                    <x-text-input id="last_name" type="text" name="last_name" :value="old('last_name')" required
                        placeholder="Enter driver's last name " />

                    <x-input-label for="email" value="Email Address" />
                    <x-text-input id="email" type="email" name="email" :value="old('email')" required
                        placeholder="e.g. driver@example.com" />


                    <x-input-label for="password" value="Password" />
                    <x-text-input id="password" type="password" name="password" :value="old('password')" required
                        placeholder="Enter driver password" />


                    <x-input-label for="phone_number" value="Phone Number" />
                    <x-text-input id="phone_number" type="text" name="phone_number" :value="old('phone_number')" required
                        placeholder="Enter phone number" /> 



                    <x-input-label for="country" value="Country" />
                    <x-text-input id="country" type="text" name="country" :value="old('country')" required
                        placeholder="Enter the country" /> 


                    <x-input-label for="address" value="Address" />
                    <x-text-input id="address" type="text" name="address" :value="old('address')"
                        placeholder="Enter driver's address" />

                    <x-input-label for="age" value="Age" />
                    <x-text-input id="age" type="number" name="age" :value="old('age')" placeholder="e.g. 25" />
                </div>

               
                <div class="right">
                    <x-input-label for="date_of_hire" value="Date of Hire" />
                    <x-text-input id="date_of_hire" type="date" name="date_of_hire" :value="old('date_of_hire')" />


                    <x-input-label for="license_category" value="License Category" />
                    <select id="license_category" name="license_category"
                        class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">-- Select Category --</option>
                        <option value="A" @selected(old('license_category') == 'A')>Category A </option>
                        <option value="B" @selected(old('license_category') == 'B')>Category B </option>
                    </select>

                    <x-input-label for="experience" value="Experience" />
                    <textarea id="experience" name="experience"
                        class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        rows="3" placeholder="Describe driver's experience">{{ old('experience') }}</textarea>

                    <x-input-label for="status" value="Status" />
                    <select id="status" name="status"
                        class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="active" @selected(old('status') == 'Available')>Available</option>
                        <option value="inactive" @selected(old('status') == 'Unavailable')>Unavailable</option>
                    </select>
                </div>
            </div>

            
            <div class="mt-6">
                <x-input-label for="license_image" value="Driver's License Image" />
              
                <input type="file" id="license_image" name="license_image" accept="image/png, image/jpeg, image/jpg"
                    class="mt-1 block w-full text-sm text-gray-500
                    file:mr-4 file:py-2 file:px-4
                    file:rounded-full file:border-0
                    file:text-sm file:font-semibold
                    file:bg-blue-50 file:text-blue-700
                    hover:file:bg-blue-100" required>
                <p class="mt-1 text-xs text-gray-500">PNG, JPG, JPEG up to 2MB.</p>
            </div>

            
            <div class="popup-buttons mt-8">
                <button type="submit" class="btn btn-primary">Save Driver</button>
                <a href="{{ route('drivers.index') }}" class="cancel-btn">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>-->

