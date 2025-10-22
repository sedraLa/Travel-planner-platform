<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/transport.css') }}"> 
        <link rel="stylesheet" href="{{ asset('css/vehicles.css') }}">
    @endpush

    <div class="vehicle-form-container"> 
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Add New Destination</h2>

        <form action="{{ route('destinations.store') }}" method="post" enctype="multipart/form-data">
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
                    <x-input-label for="name" value="Destination Name" />
                    <x-text-input id="name" type="text" name="name" :value="old('name')" required
                        placeholder="Enter  Destination name" />


                    <x-input-label for="city" value="City" />
                    <x-text-input id="city" type="text" name="city" :value="old('city')" required
                        placeholder="Entre City" />

                    <x-input-label for="country" value="Country" />
                    <x-text-input id="country" type="text" name="country" :value="old('country')" required
                        placeholder="Enter country" />


                    <x-input-label for="description" value="Description" />
                    <x-text-input id="description" type="text" name="description" :value="old('description')" required
                        placeholder="write a description" />

                    <x-input-label for="location_details" value="location_details" />
                    <x-text-input id="location_details" type="text" name="location_details" :value="old('location_details')" required
                        placeholder="Enter location_details" />

                    <!--
                    <x-input-label for="activities" value="activities" />
                    <x-text-input id="activities" type="text" name="activities" :value="old('activities')" required
                        placeholder="Enter activities" />-->


                   <x-input-label for="iata_code" :value="__('IATA Code (for the most famous airport in this destination)')" />
                     <p>you can find the right IATA code here <a target='_blank' href="https://airportcodes.aero/search">IATA-CODES</a></p>
                     <x-text-input id="iata_code" name="iata_code" type="text" class="mt-1 block w-full" maxlength="3" required />
                     <x-input-error class="mt-2" :messages="$errors->get('iata_code')" />


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
                    <x-input-label for="timezone" value="timezone" />
                    <x-text-input id="timezone" type="text" name="timezone" required :value="old('timezone')" />


                    <x-input-label for="language" value="language" />
                    <x-text-input id="language" type="text" name="language"  required :value="old('language')" />

                    <x-input-label for="currency" value="currency" />
                    <x-text-input id="currency" type="text" name="currency" required :value="old('currency')" />

                    <x-input-label for="nearest_airport" value="nearest_airport" />
                    <x-text-input id="nearest_airport" type="text" name="nearest_airport"  required :value="old('nearest_airport')" />

                    <x-input-label for="best_time_to_visit" value="best_time_to_visit" />
                    <x-text-input id="best_time_to_visit" type="text" name="best_time_to_visit" required :value="old('best_time_to_visit')" />


                    <x-input-label for="emergency_numbers" value="emergency_numbers" />
                    <x-text-input id="emergency_numbers" type="text" name="emergency_numbers" required :value="old('emergency_numbers')" />

                    <x-input-label for="local_tip" value="local_tip" />
                    <x-text-input id="local_tip" type="text" name="local_tip" required :value="old('local_tip')" />


                    <div class="mt-6">
                     <x-input-label for="highlight" required value="Highlight" />

                      <div id="highlights-wrapper">
                      <div class="highlight-item flex items-center mb-2">
                       <input type="text" name="highlight[]" 
                            class="block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                            placeholder="Enter a highlight (e.g., Famous landmark)">
                          <button type="button" onclick="addHighlightField()" 
                          class="ml-2 px-3 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">+</button>
                         </div>
                        </div>

                      <p class="text-sm text-gray-500 mt-1">Click (+) to add more highlights</p>
                    
                 </div>
                 
                </div>

            </div>
             
                  
            
                   <div class="popup-buttons mt-8">
                   <button type="submit" class="btn btn-primary">Create Destination</button>
                   <a href="{{ route('drivers.index') }}" class="cancel-btn">Cancel</a>
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
    function addHighlightField() {
        const wrapper = document.getElementById('highlights-wrapper');

        const div = document.createElement('div');
        div.classList.add('highlight-item', 'flex', 'items-center', 'mb-2');

        div.innerHTML = `
            <input type="text" name="highlights[]" 
                   class="block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                   placeholder="Enter another highlight">
            <button type="button" onclick="removeHighlightField(this)" 
                    class="ml-2 px-3 py-2 bg-red-500 text-white rounded-md hover:bg-red-600">−</button>
        `;

        wrapper.appendChild(div);
    }

    function removeHighlightField(button) {
        button.parentElement.remove();
    }
</script>