<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/transport.css') }}"> 
        <link rel="stylesheet" href="{{ asset('css/vehicles.css') }}">
    @endpush


    <div class="vehicle-form-container"> 
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Edit Hotel</h2>

        <form action="{{ route('hotels.update', $hotel) }}" method="post" enctype="multipart/form-data">
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
                    <x-input-label for="name" value="Hotel Name" />
                    <x-text-input id="name" type="text" name="name" :value="old('name', $hotel->name)" required
                        placeholder="Enter  hotel name" />


                    <div class="mt-4">
                         <x-input-label for="destination_id" value="Associated destination" />
                              <select name="destination_id"  id="destination_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                 <option value="">-- Select the associated destination --</option>
                                    @foreach($destinations as $destination)
                                     <option value="{{ $destination->id }}"
                                       data-city="{{ $destination->city }}"
                                        data-country="{{ $destination->country }}"
                                        {{ old('destination_id', $hotel->destination_id) == $destination->id ? 'selected' : '' }}
                                      >{{ $destination->name }}</option>
                                    @endforeach
                                </select>
                        </div>    
                    <x-input-label for="city" value="City" />
                    <x-text-input id="city" type="text" name="city" :value="old('city',$hotel->city)" required
                        placeholder="Entre City" />

                    <x-input-label for="country" value="Country" />
                    <x-text-input id="country" type="text" name="country" :value="old('country', $hotel->country)" required
                        placeholder="Enter country" />


                    <x-input-label for="description" value="Description" />
                    <x-text-input id="description" type="text" name="description" :value="old('description', $hotel->description)" required
                        placeholder="write a description" />

                    <x-input-label for="address" value="address" />
                    <x-text-input id="address" type="text" name="address" :value="old('address', $hotel->address)" required
                        placeholder="address" />


                    <x-input-label for="global_rating" value="global_rating" />
                    <x-text-input id="global_rating" type="number" name="global_rating"  min="0"  max="5"  step="0.5"  :value="old('global_rating', $hotel->global_rating)" required
                        placeholder=" global_rating (max:5) " />

                    <x-input-label for="price_per_night" value="price_per_night" />
                    <x-text-input id="price_per_night" type="number" name="price_per_night"  min="0"  step="0.01"   :value="old('price_per_night', $hotel->price_per_night)" required
                        placeholder=" price_per_night" />


                    <x-input-label for="total_rooms" value="total_rooms" />
                    <x-text-input id="total_rooms" type="number" name="total_rooms"  min="0"  :value="old('total_rooms', $hotel->total_rooms)" required
                    placeholder=" total_rooms" />


                    <x-input-label for="stars" value="stars" />
                    <select id="stars" name="stars" class="block w-full mt-1 border-gray-300 rounded-md" required>
                           <option value="" disabled  selected>Select how many stars stars</option>
                           <option value="1" {{ old('stars', $hotel->stars) == 1 ? 'selected' : '' }}>🌟 Star</option>
                           <option value="2" {{ old('stars', $hotel->stars) == 2 ? 'selected' : '' }}> 🌟🌟Stars</option>
                           <option value="3" {{ old('stars', $hotel->stars) == 3 ? 'selected' : '' }}>🌟🌟🌟 Stars</option>
                           <option value="4" {{ old('stars', $hotel->stars) == 4 ? 'selected' : '' }}>🌟🌟🌟🌟Stars</option>
                           <option value="5" {{ old('stars', $hotel->stars) == 5 ? 'selected' : '' }}>🌟🌟🌟🌟🌟Stars</option>
                    </select>

                      <!--image upload-->
                 
                 <div class="mt-6">
    <x-input-label for="images" :value="__('Images')" />

    <input id="images" name="images[]" type="file"
        class="mt-1 block w-full text-sm text-gray-500
        file:mr-4 file:py-2 file:px-4
        file:rounded-full file:border-0
        file:text-sm file:font-semibold
        file:bg-blue-50 file:text-blue-700
        hover:file:bg-blue-100"
        multiple onchange="addFilesToInput(this)" />

    <x-input-error class="mt-2" :messages="$errors->get('images')" />
</div>


                     
                </div>

               
                <div class="right">
                   <x-input-label for="pets_allowed" value="Pets Allowed" />
                    <select id="pets_allowed" name="pets_allowed" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                           <option value=""  disabled  selected>-- Select --</option>
                           <option value="allowed" {{ old('pets_allowed', $hotel->pets_allowed) == 'allowed' ? 'selected' : '' }}>pets allowed</option>
                           <option value="not_allowed" {{ old('pets_allowed',$hotel->pets_allowed) == 'not_allowed' ? 'selected' : '' }}>pets not allowed</option>
                    </select>

                    <x-input-label for="check_in_time" value="check_in_time" />
                    <x-text-input id="check_in_time" type="time" name="check_in_time" required :value="old('check_in_time', \Carbon\Carbon::parse($hotel->check_in_time)->format('H:i'))"/>

                    <x-input-label for="check_out_time" value="check_out_time" />
                    <x-text-input id="check_out_time" type="time" name="check_out_time" required :value="old('check_out_time', \Carbon\Carbon::parse($hotel->check_out_time)->format('H:i'))" />

                    <x-input-label for="policies" value="policies" />
                    <x-text-input id="policies" type="text" name="policies"  required :value="old('policies', $hotel->policies)" />

                    <x-input-label for="phone_number" value="phone_number" />
                    <x-text-input id="phone_number" type="number" name="phone_number" required :value="old('phone_number', $hotel->phone_number)" />

                    <x-input-label for="email" value="email" />
                    <x-text-input id="email" type="email" name="email" required :value="old('email', $hotel->email)" />

                    
                    <x-input-label for="website" value="website" />
                    <x-text-input id="website" type="url" name="website" required :value="old('website', $hotel->website)" />


                    <x-input-label for="nearby_landmarks" value="nearby_landmarks" />
                    <x-text-input id="nearby_landmarks" type="text" name="nearby_landmarks" :value="old('nearby_landmarks',$hotel->nearby_landmarks)" required/>
                    
                        <x-input-label for="amenities" value="Amenities" />
                             @php
                               $options = ['Wifi','Parking','Pool','Spa','Restaurant','Gym','Laundry','Air Condition','Free Breakfast'];
                               $oldAmenities = old('amenities', $hotel->amenities ?? []);   
                             @endphp

                        <div class="amenities-container">
                            @foreach($options as $option)
                              <label class="custom-option">
                               <input  type="checkbox" name="amenities[]"  value="{{ $option }}" 
                                   {{ in_array($option, $oldAmenities) ? 'checked' : '' }}>
                                         <span>{{ $option }}</span>
                              </label>
                            @endforeach

                        </div> 
                </div>
            </div>
             

            
                   <div class="popup-buttons mt-8">
                   <button type="submit" class="btn btn-primary">Update Hotel</button>
                   <a href="{{ route('hotels.index') }}" class="cancel-btn">Cancel</a>
                   </div>
        </form>



        {{-- show current images --}}
@if($hotel->images->count())
<div class="mt-10">
    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Current Images</h3>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @foreach($hotel->images as $image)
            <div class="relative group border rounded shadow overflow-hidden h-48 bg-white dark:bg-gray-900">

                {{-- show image --}}
                <img src="{{ asset('storage/' . $image->image_url) }}"
                class="w-full h-full object-cover" alt="Hotel Image">

                {{-- delete image button--}}
                <form action="{{ route('hotel-images.destroy', $image->id) }}" method="POST"
                    class="absolute top-2 right-2 z-10">
                  @csrf
                  @method('DELETE')
                  <button type="submit"
                          class="bg-red-600 text-white text-xs px-2 py-1 rounded hover:bg-red-700 shadow transition">
                      ✕
                  </button>
              </form>

            {{-- set primary image --}}
               @if (!$image->is_primary)
                 <form action="{{ route('hotel-images.setPrimary', $image->id) }}" method="POST"
                        class="absolute bottom-2 left-2 z-10">
                        @csrf
                        <button type="submit"
                        class="bg-blue-600 text-white text-xs px-3 py-1 rounded hover:bg-blue-700 shadow transition">
                             Set as Primary
                        </button>
                </form>
            @else
                <span class="absolute bottom-2 left-2 bg-green-600 text-white text-xs px-3 py-1 rounded shadow">
                     Primary
                </span>
            @endif

            </div>
        @endforeach
    </div>
</div>
        @endif
            </div>
        </div>
    </div>

    

</x-app-layout>


<script>
let allFiles = [];

function addFilesToInput(input) {
    const newFiles = Array.from(input.files);
    allFiles = allFiles.concat(newFiles); // نضيف الجديد مع القديم

    // إعادة بناء القائمة كلها داخل input
    const dataTransfer = new DataTransfer();
    allFiles.forEach(file => dataTransfer.items.add(file));

    input.files = dataTransfer.files;
}
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const destinationSelect = document.getElementById('destination_id');
    const cityInput = document.getElementById('city');
    const countryInput = document.getElementById('country');

    // هل الصفحة رجعت مع old values؟
    const hasOldValues = "{{ old('city') || old('country') ? '1' : '0' }}" === '1';

    function syncCityCountry() {
        const option = destinationSelect.selectedOptions[0];
        if (!option) return;

        cityInput.value = option.dataset.city || '';
        countryInput.value = option.dataset.country || '';
    }

    // فقط إذا ما في old values
    if (!hasOldValues && destinationSelect.value) {
        syncCityCountry();
    }

    // عند تغيير الـ destination دائماً حدّث
    destinationSelect.addEventListener('change', function () {
        syncCityCountry();
    });
});
</script>
