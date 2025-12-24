
<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/transport.css') }}">
        <link rel="stylesheet" href="{{ asset('css/vehicles.css') }}">


    @endpush



    <div class="vehicle-form-container">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Add New Hotel</h2>

        <form action="{{ route('hotels.store') }}" method="post" enctype="multipart/form-data">
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
                    <x-input-label for="name" value="Hotel Name" />
                    <x-text-input id="name" type="text" name="name" :value="old('name')" required
                        placeholder="Enter Hotel name" />


                        <div class="mt-4">
                         <x-input-label for="destination_id" value="Associated destination" />
                              <select name="destination_id"  id="destination_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                 <option value="">-- Select the associated destination --</option>
                                    @foreach($destinations as $destination)
                                     <option value="{{ $destination->id }}"
                                       data-city="{{ $destination->city }}"
                                        data-country="{{ $destination->country }}"
                                      >{{ $destination->name }}</option>
                                    @endforeach
                                </select>
                        </div>

                    <x-input-label for="city" value="City" />
                    <x-text-input id="city" type="text" name="city" :value="old('city')" required/>


                    <x-input-label for="country" value="Country" />
                    <x-text-input id="country" type="text" name="country" :value="old('country')" required/>




                    <x-input-label for="description" value="Description" />
                    <x-text-input id="description" type="text" name="description" :value="old('description')" required
                        placeholder="write a description" />

                    <x-input-label for="address" value="address" />
                    <x-text-input id="address" type="text" name="address" :value="old('address')" required
                        placeholder="Enter address" />

                     <x-input-label for="global_rating" value="global_rating" />
                    <x-text-input id="global_rating" type="number" name="global_rating"  min="0"  max="5"  step="0.5" :value="old('global_rating')" required
                        placeholder="Enter global_rating(max:5)" />

                     <x-input-label for="price_per_night" value="price_per_night" />
                     <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 text-sm">$</span>
                    <x-text-input id="price_per_night"  type="number" name="price_per_night" :value="old('price_per_night')" required
                         step="0.01" placeholder="Enter price_per_night $" />

                    <x-input-label for="total_rooms" value="total_rooms" />
                    <x-text-input id="total_rooms" type="number" name="total_rooms" step="1" min="0"  :value="old('total_rooms')" required
                        placeholder="Enter total_rooms" />

                    <x-input-label for="stars" value="stars" />
                    <select id="stars" name="stars" class="block w-full mt-1 border-gray-300 rounded-md" required>
                           <option value="" disabled  selected>Select how many stars stars</option>
                           <option value="1" {{ old('stars') == 1 ? 'selected' : '' }}>🌟 Star</option>
                           <option value="2" {{ old('stars') == 2 ? 'selected' : '' }}> 🌟🌟Stars</option>
                           <option value="3" {{ old('stars') == 3 ? 'selected' : '' }}>🌟🌟🌟 Stars</option>
                           <option value="4" {{ old('stars') == 4 ? 'selected' : '' }}>🌟🌟🌟🌟Stars</option>
                           <option value="5" {{ old('stars') == 5 ? 'selected' : '' }}>🌟🌟🌟🌟🌟Stars</option>
                    </select>





                     <div class="flex space-x-4 mt-4">
                   <!-- Images Upload -->
                   <div class="mt-6">
                   <x-input-label for="images" :value="__('Images')" />
                   <input id="images" name="images[]" type="file" class="mt-1 block w-full text-sm text-gray-500
                    file:mr-4 file:py-2 file:px-4
                    file:rounded-full file:border-0
                    file:text-sm file:font-semibold
                    file:bg-blue-50 file:text-blue-700
                    hover:file:bg-blue-100"  multiple onchange="showPrimarySelect(this)" />
                   <x-input-error class="mt-2" :messages="$errors->get('images')" />
                   </div>
                   </div>

                   <!-- Primary Image -->
                   <div id="primary-select-wrapper" class="mt-4 hidden">
                   <x-input-label for="primary_image_index" :value="__('Choose Primary Image')" />
                   <!--select primary image-->
                   <select name="primary_image_index" id="primary_image_index" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm"></select>
                   <x-input-error class="mt-2" :messages="$errors->get('primary_image_index')" />
                   </div>



                </div>


                <div class="right">


                    <x-input-label for="pets_allowed" value="Pets Allowed" />
                    <select id="pets_allowed" name="pets_allowed" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                           <option value=""  disabled  selected>-- Select --</option>
                           <option value="1" {{ old('pets_allowed') == 'allowed' ? 'selected' : '' }}>pets allowed</option>
                           <option value="0" {{ old('pets_allowed') == 'not_allowed' ? 'selected' : '' }}>pets not allowed</option>
                    </select>

                    <x-input-label for="check_in_time" value="check_in_time" />
                    <x-text-input id="check_in_time" type="time" name="check_in_time" :value="old('check_in_time')" required
                        placeholder="Enter check in time" />


                    <x-input-label for="check_out_time" value="check_out_time" />
                    <x-text-input id="check_out_time" type="time" name="check_out_time" :value="old('check_out_time')" required
                        placeholder="Enter check out time" />

                    <x-input-label for="policies" value="policies" />
                    <x-text-input id="policies" type="text" name="policies" :value="old('policies')" required
                        placeholder=" Enter policies" />

                    <x-input-label for="phone_number" value="phone_number" />
                    <x-text-input id="phone_number" type="number" name="phone_number" :value="old('phone_number')" required
                        placeholder="phone_number" />


                    <x-input-label for="email" value="email" />
                    <x-text-input id="email" type="email" name="email" :value="old('email')" required
                        placeholder="Enter @email.com" />



                   <x-input-label for="website" value="website" />
                    <x-text-input id="website" type="url" name="website" :value="old('website')" required
                        placeholder="Enter A website" />


                    <x-input-label for="nearby_landmarks" value="nearby_landmarks" />
                    <x-text-input id="nearby_landmarks" type="text" name="nearby_landmarks" :value="old('nearby_landmarks')" required
                        placeholder="Enter nearby landmarks" />



                        <x-input-label for="amenities" value="Amenities" style="margin-bottom:10px;"/>
                        @php
                             $options = ['Wifi', 'Parking', 'Pool', 'Spa', 'Restaurant', 'Gym', 'Laundry', 'Air Condition', 'Free Breakfast'];
                             $oldAmenities = old('amenities', []);
                        @endphp

                     <div class="amenities-container">
                         @foreach($options as $option)
                             <label class="custom-option" style="display:flex; gap:8px; ">
                               <input type="checkbox" name="amenities[]" value="{{ $option }}"{{ in_array($option, $oldAmenities) ? 'checked' : '' }} style="width:30px; height:30px;border-radius:15px; font-size:12px;">
                                 <span >{{ $option }}</span>
                             </label>
                         @endforeach
                     </div>
                </div>

            </div>

                   <div class="popup-buttons mt-8">
                   <button type="submit" class="btn btn-primary">Create</button>
                   <a href="{{ route('hotels.index') }}" class="cancel-btn">Cancel</a>
                   </div>
        </form>
    </div>
</x-app-layout>








<script>
    let allFiles = [];

    function showPrimarySelect(input) {
        const newFiles = Array.from(input.files);
        allFiles = allFiles.concat(newFiles); // نضيف الصور الجديدة بدون حذف القديمة

        const select = document.getElementById('primary_image_index');
        const wrapper = document.getElementById('primary-select-wrapper');

        if (allFiles.length > 0) {
            select.innerHTML = '';
            wrapper.classList.remove('hidden');

            allFiles.forEach((file, i) => {
                const option = document.createElement('option');
                option.value = i;
                option.textContent = file.name;
                select.appendChild(option);
            });
        } else {
            wrapper.classList.add('hidden');
        }

        //  تحديث ملفات الفورم المخفية
        updateFileList(input);
    }

    function updateFileList(input) {
        // إنشاء كائن جديد من نوع DataTransfer لتخزين الملفات كلها
        const dataTransfer = new DataTransfer();
        allFiles.forEach(file => dataTransfer.items.add(file));
        input.files = dataTransfer.files;
    }
    </script>


<script>
document.addEventListener('DOMContentLoaded', function() {
    const destinationSelect = document.getElementById('destination_id');
    const cityInput = document.getElementById('city');
    const countryInput = document.getElementById('country');

    // عند تغيير الـ select
    destinationSelect.addEventListener('change', function() {
        // عبّي الحقول فقط إذا فارغة
        if (!cityInput.value) {
            cityInput.value = this.selectedOptions[0].dataset.city || '';
        }
        if (!countryInput.value) {
            countryInput.value = this.selectedOptions[0].dataset.country || '';
        }
    });

    // تعيين القيم الأولية عند التحميل
    const oldCity = "{{ old('city', $hotel->city ?? '') }}";
    const oldCountry = "{{ old('country', $hotel->country ?? '') }}";

    cityInput.value = oldCity;
    countryInput.value = oldCountry;
});

</script>
