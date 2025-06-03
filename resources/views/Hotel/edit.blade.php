<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Edit Hotel') }}
            </h2>
            <a href="{{ route('hotels.index') }}"
               class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded shadow transition duration-200">
                ← Back to Hotels
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-7xl">
                    @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-100 text-red-700 rounded text-sm">
                        @foreach ($errors->all() as $error)
                            <div class="mb-1">• {{ $error }}</div>
                        @endforeach
                    </div>
                @endif
                {{--success message--}}
                @if (session('success'))
                <div class="mb-4 px-4 py-3 bg-green-100 text-green-800 rounded">
                    {{ session('success') }}
                </div>
            @endif
                
        {{--update form--}}

        <form method="POST" action="{{route('hotels.update',$hotel->id)}}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!--fields-->
            <div class="flex space-x-4">
                <div class="w-1/2">
                    <!--hotel name -->
                    <x-input-label for="name" value="Hotel Name"/>
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                  value="{{ old('name', $hotel->name) }}" required/>
                </div>

                <div class="w-1/2">
                    <!--hotel description -->
                    <x-input-label for="description" value="Hotel Description (optional)"/>
                    <textarea  id="description" name="description"  class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">{{old('description',$hotel->description) }}</textarea>

                </div>
            </div>

             <!--address-->
        <div class="flex space-x-4 mt-4">
            <div class="w-1/2">
                <x-input-label for="address" value="Address"/>
                <x-text-input id="address" name="address" type="text" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm"
                              value="{{ old('address', $hotel->address) }}" required/>
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
                     value= "{{old('price_per_night', $hotel->price_per_night)}}"
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
        required>

        <option value="">-- Select the associated destination --</option>
        @foreach($destinations as $destination)
            <option value="{{ $destination->id }}"
            data-city="{{ $destination->city }}"
            data-country="{{ $destination->country }}"
            @if(old('destination_id',$hotel->destination_id) == $destination->id)
            selected
            @endif
                >{{ $destination->name }}</option>
        @endforeach
    </select>
</div>

<!--city & country-->
<div class="flex space-x-4 mt-4">
    <div class="w-1/2">
<x-input-label for="city" value="City" />
   <x-text-input id="city" name="city" type="text" class="mt-1 block w-full" value="{{old('city',$hotel->destination->city ?? '')}}" required />
     </div>
      <div class="w-1/2">
        <x-input-label for="country" value="Country" />
  <x-text-input id="country" name="country" type="text" class="mt-1 block w-full" value="{{old('country',$hotel->destination->country ?? '')}}" required />
          </div>
     </div>

     <!--global rating-->
     <div class="flex space-x-4 mt-4">
        <div class="w-1/2">
            <x-input-label for="global_rating" value="Global Rating"/>
            <input id="global_rating" type="number" name="global_rating" step="1" min="1" max="5" value="{{old('global_rating',$hotel->global_rating)}}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 p-3 text-sm" placeholder="Enter a rating from 1 to 5" />
        </div>
        <!--total rooms-->
        <div class="w-1/2">
            <x-input-label for="total_rooms" value="Total Rooms" />
            <input id="total_rooms" type="number" name="total_rooms" step="1" min="0" required value="{{old('total_rooms',$hotel->total_rooms)}}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 p-3 text-sm" placeholder="Enter number of total rooms of the hotel" />
        </div>
                </div>


        <!--image upload-->
        <!-- upload new images -->
        <div class="mb-4">
            <x-input-label value="Upload New Images"/>
            <div id="image-inputs">
                <input type="file" name="images[]" onchange="addImageInput()" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" multiple >
            </div>
        </div>

        <!-------------cancel & update buttons------------------>

        <div class="flex items-center justify-end mt-4 space-x-3">
            <a href="{{ route('hotels.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-black dark:text-white uppercase tracking-widest hover:bg-gray-400 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition ease-in-out duration-150">
                {{ __('Cancel') }}
            </a>
            <x-primary-button>
                {{ __('Update') }}
            </x-primary-button>
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
    let imageIndex = 0;
    function addImageInput() {
    const container = document.getElementById('image-inputs');
    const input = document.createElement('input');
    input.type = 'file';
    input.name = 'images[]';
    input.onchange = addImageInput;
    input.classList.add('block', 'mt-2');
    container.appendChild(input);

    updatePrimarySelect();
}

    </script>

