<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Create New Hotel') }}
            </h2>
            <a href="{{ route('hotels.index') }}"
               class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded shadow transition duration-200">
                ← Back to hotels
            </a>
        </div>
    </x-slot>

    <!--create form-->
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">

    <form action="{{route('hotels.store')}}" method="post" enctype="multipart/form-data">
        @csrf

        <!--fields-->
        <div class="flex space-x-4">
            <div class="w-1/2">
        <!--hotel name & description-->
        <x-input-label for="name"  value="Hotel Name"/>
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" required autofocus />
            </div>
            <div class="w-1/2">
                <x-input-label for="description" value="Hotel Descreption (optional)" />
                <textarea id="description" name="description" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm"></textarea>
            </div>
        </div>

        <!--address-->
        <div class="flex space-x-4 mt-4">
            <div class="w-1/2">
               <x-input-label for="address" value="Hotel Address"/>
               <x-text-input id="address" type="text" name="addresss" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm" required />
            </div>
                    <!--price per night-->
                    <div class="w-1/2">
                        <x-input-label for="price_per_night" value="Price Per Night" />
                        <div class="relative">
                         <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 text-sm">$</span>
                         <input
                             type="number"
                             name="price_per_night"
                             id="price_per_night"
                             step="0.01"
                             required
                             placeholder="Enter price in $"
                             class="pl-8 pr-4 py-2 w-full border border-gray-300 rounded-xl shadow-sm focus:border-indigo-500 focus:ring-indigo-200 focus:ring focus:ring-opacity-50 text-sm"
                         />
                 </div>
             </div>
        </div>


       <!-- choose destination-->
<div class="mt-4">
    <x-input-label for="destination_id" value="Associated destination" />
    <select
        name="destination_id"
        id="destination_id"
        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
        required
    >
        <option value="">-- Select the associated destination --</option>
        @foreach($destinations as $destination)
            <option value="{{ $destination->id }}"
            data-city="{{ $destination->city }}" 
            data-country="{{ $destination->country }}"
                >{{ $destination->name }}</option>
        @endforeach
    </select>
</div>

        <!--city & country-->
        <div class="flex space-x-4 mt-4">
            <div class="w-1/2">
        <x-input-label for="city" value="City" />
           <x-text-input id="city" name="city" type="text" class="mt-1 block w-full" required />
             </div>
              <div class="w-1/2">
                <x-input-label for="country" value="Country" />
          <x-text-input id="country" name="country" type="text" class="mt-1 block w-full" required />
                  </div>
             </div>

        <!--global rating-->
        <div class="flex space-x-4 mt-4">
            <div class="w-1/2">
                <x-input-label for="rating" value="Global Rating"/>
                <input id="rating" type="number" name="rating" step="1" min="1" max="5" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 p-3 text-sm" placeholder="Enter a rating from 1 to 5" />
            </div>

         <!--total rooms-->
        <div class="w-1/2">
            <x-input-label for="total_rooms" value="Total Rooms" />
            <input id="total_rooms" type="number" name="total_rooms" step="1" min="0" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 p-3 text-sm" placeholder="Enter number of total rooms of the hotel" />
        </div>
        </div>

        

    <!--image upload-->
<!-- Input images -->
<div class="mb-4">
    <x-input-label for="images" value="Hotel Images" />
    <input
        id="images"
        type="file"
        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
        multiple
        onchange="handleFileSelect(event)"
    />
</div>

<!-- choose primary image-->
<div id="primary-select-wrapper" class="mt-4 hidden">
    <x-input-label for="primary_image_index" value="Choose primary image" />
    <select
        name="primary_image_index"
        id="primary_image_index"
        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
    ></select>
</div>

<!-- hidden field contain all images-->

<input type="file" id="real-images" name="images[]" multiple hidden />

<<!--cancel & submit button-->
<div class="flex items-center justify-end mt-4 space-x-3">
    <!-- Cancel Button -->
     <a href="{{ route('hotels.index') }}"
     class="inline-flex items-center px-4 py-2 bg-gray-300 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-black dark:text-white uppercase tracking-widest hover:bg-gray-400 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition ease-in-out duration-150">
        {{ __('Cancel') }}
               </a>
           <!-- Submit Button -->
              <x-primary-button>
       {{ __('Create Hotel') }}
            </x-primary-button>
                     </div>






</div>
</div>
</div>





<script>
    let allFiles = [];

    function handleFileSelect(event) {
        const input = event.target;
        const newFiles = Array.from(input.files);

        // Combine old with new images
        allFiles = [...allFiles, ...newFiles];

        // Update hidden input to be sent with the form
        const dataTransfer = new DataTransfer();
        allFiles.forEach(file => dataTransfer.items.add(file));
        document.getElementById('real-images').files = dataTransfer.files;

        // Update select menu
        const select = document.getElementById('primary_image_index');
        const wrapper = document.getElementById('primary-select-wrapper');
        select.innerHTML = "";

        allFiles.forEach((file, index) => {
            const option = document.createElement('option');
            option.value = index;
            option.text = `image ${index + 1} - ${file.name}`;
            select.appendChild(option);
        });

        wrapper.classList.remove('hidden');
    }

    document.addEventListener('DOMContentLoaded', function () {

        // prevent sending if the admin didn't upload at least one image

        document.querySelector('form').addEventListener('submit', function (e) {
            const imageInput = document.getElementById('real-images');
            if (imageInput.files.length === 0) {
                e.preventDefault();
                alert('please upload at least one image before sending the form');
            }
        });

        // fill city & country field after choosing the destination
        const destinationSelect = document.getElementById('destination_id');
        const cityInput = document.getElementById('city');
        const countryInput = document.getElementById('country');

        destinationSelect.addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            const city = selectedOption.getAttribute('data-city');
            const country = selectedOption.getAttribute('data-country');

            cityInput.value = city || '';
            countryInput.value = country || '';
        });
    });
</script>

    
    








    </form>


</x-app-layout>
