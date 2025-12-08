<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/transport.css') }}"> 
        <link rel="stylesheet" href="{{ asset('css/vehicles.css') }}">
    @endpush

    <div class="vehicle-form-container"> 
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Edit Destination</h2>

        <form action="{{ route('destinations.update', $destination->id) }}" method="post" enctype="multipart/form-data">
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
                    <x-input-label for="name" value="Destination Name" />
                    <x-text-input id="name" type="text" name="name" :value="old('name', $destination->name)" required
                        placeholder="Enter  Destination name" />


                    <x-input-label for="city" value="City" />
                    <x-text-input id="city" type="text" name="city" :value="old('city',$destination->city)" required
                        placeholder="Entre City" />

                    <x-input-label for="country" value="Country" />
                    <x-text-input id="country" type="text" name="country" :value="old('country', $destination->country)" required
                        placeholder="Enter country" />


                    <x-input-label for="description" value="Description" />
                    <x-text-input id="description" type="text" name="description" :value="old('description', $destination->description)" required
                        placeholder="write a description" />

                    <x-input-label for="location_details" value="location_details" />
                    <x-text-input id="location_details" type="text" name="location_details" :value="old('location_details', $destination->location_details)" required
                        placeholder="Enter location_details" />

                    <!--
                    <x-input-label for="activities" value="activities" />
                    <x-text-input id="activities" type="text" name="activities" :value="old('activities')" required
                        placeholder="Enter activities" />-->


                   <x-input-label for="iata_code" :value="('IATA Code (for the most famous airport in this destination)')" />
                     <p>you can find the right IATA code here <a target='_blank' href="https://airportcodes.aero/search">IATA-CODES</a></p>
                     <x-text-input id="iata_code" name="iata_code" type="text" class="mt-1 block w-full" maxlength="3" required  value="{{old('iata_code', $destination->iata_code)}}"  />
                     <x-input-error class="mt-2" :messages="$errors->get('iata_code')" />


                     
                   <!-- imageee uploadd-->

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
                    <x-input-label for="timezone" value="timezone" />
                    <x-text-input id="timezone" type="text" name="timezone" required :value="old('timezone', $destination->timezone)" />


                    <x-input-label for="language" value="language" />
                    <x-text-input id="language" type="text" name="language" required :value="old('language', $destination->language)" />

                    <x-input-label for="currency" value="currency" />
                    <x-text-input id="currency" type="text" name="currency" required :value="old('currency', $destination->currency)" />

                    <x-input-label for="nearest_airport" value="nearest_airport" />
                    <x-text-input id="nearest_airport" type="text" name="nearest_airport"  required :value="old('nearest_airport', $destination->nearest_airport)" />

                    <x-input-label for="best_time_to_visit" value="best_time_to_visit" />
                    <x-text-input id="best_time_to_visit" type="text" name="best_time_to_visit" required :value="old('best_time_to_visit', $destination->best_time_to_visit)" />


                    <x-input-label for="emergency_numbers" value="emergency_numbers" />
                    <x-text-input id="emergency_numbers" type="text" name="emergency_numbers" required :value="old('emergency_numbers', $destination->emergency_numbers)" />

                    <x-input-label for="local_tip" value="local_tip" />
                    <x-text-input id="local_tip" type="text" name="local_tip" required :value="old('local_tip', $destination->local_tip)" />


                    <div class="mt-6">
                     <x-input-label for="highlight"  required value="Highlight" />
                              @php
                                 // نجلب النصوص من العلاقة ونحولها لمصفوفة
                                $existingHighlights = old('highlight', $destination->highlights->pluck('title')->toArray() ?? []);
                              @endphp

                           <div id="highlights-wrapper">
                                 @foreach ($existingHighlights as $highlight)
                                   <div class="highlight-item flex items-center mb-2">
                                   <input type="text" name="highlight[]" 
                                      class="block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                      value="{{ $highlight }}"
                                     placeholder="Enter a highlight (e.g., Famous landmark)">
                                   <button type="button" onclick="removeHighlightField(this)" 
                                class="ml-2 px-3 py-2 bg-red-500 text-white rounded-md hover:bg-red-600">−</button>
                            </div>
                                   @endforeach
                            </div>

                   <p class="text-sm text-gray-500 mt-1">Click (+) to add more highlights</p>

                   <button type="button" onclick="addHighlightField()" 
                   class="mt-2 px-3 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">+</button>




                    
                 </div>
                 
                </div>

            </div>
             
                  
            
                   <div class="popup-buttons mt-8">
                   <button type="submit" class="btn btn-primary">Update Destination</button>
                   <a href="{{ route('destination.index') }}" class="cancel-btn">Cancel</a>
                   </div>
        </form>



        {{-- show current images--}}
@if($destination->images->count())
    <div class="mt-10">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Current Images</h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($destination->images as $image)
                <div class="relative group border rounded shadow overflow-hidden h-48 bg-white dark:bg-gray-900">
                    <!-- show images-->
                    <img src="{{Str::startsWith($image->image_url,'storage/')? asset($image->image_url):asset('storage/' . $image->image_url) }}"
                         class="w-full h-full object-cover" alt="Destination Image">

                    <!-- delete image-->
                    <form action="{{ route('destination-images.destroy', $image->id) }}" method="POST"
                          class="absolute top-2 right-2 z-10">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="bg-red-600 text-white text-xs px-2 py-1 rounded hover:bg-red-700 shadow transition">
                            ✕
                        </button>
                    </form>

<!-- set image as primary-->
@if (!$image->is_primary)
    <form action="{{ route('destination-images.setPrimary', $image->id) }}" method="POST"
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


    

</x-app-layout>







<script>
    function addHighlightField() {
        const wrapper = document.getElementById('highlights-wrapper');

        const div = document.createElement('div');
        div.classList.add('highlight-item', 'flex', 'items-center', 'mb-2');

        div.innerHTML = `
            <input type="text" name="highlight[]" 
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
