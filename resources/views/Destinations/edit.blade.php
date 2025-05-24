<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Edit Destination') }}
            </h2>
            <a href="{{ route('destination.index') }}"
               class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded shadow transition duration-200">
                ← Back to Destinations
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-7xl">

                   {{-- Success Message --}}
                   @if (session('success') && session('from') !== 'set_primary')
                   <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-800 rounded">
                       {{ session('success') }}
                   </div>
               @endif

                    {{-- Error Messages --}}
                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                            <ul class="list-disc pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Update Form -->
    <!-- كل الحقول فقط، بدون جزء الصور -->


    <form method="POST" action="{{ route('destinations.update', $destination->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="flex space-x-4">
            <div class="w-1/2">
                <x-input-label for="name" :value="__('Destination Name')" />
                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" required value="{{ old('name', $destination->name) }}" />
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>

            <div class="w-1/2">
                <x-input-label for="description" :value="__('Description (Optional)')" />
                <textarea id="description" name="description" class="mt-1 block w-full dark:bg-gray-900 dark:text-gray-300">{{ old('description', $destination->description) }}</textarea>
                <x-input-error class="mt-2" :messages="$errors->get('description')" />
            </div>
        </div>

        <div class="flex space-x-4 mt-4">
            <div class="w-1/2">
                <x-input-label for="location_details" :value="__('Location Details')" />
                <textarea id="location_details" name="location_details" class="mt-1 block w-full dark:bg-gray-900 dark:text-gray-300" required>{{ old('location_details', $destination->location_details) }}</textarea>
                <x-input-error class="mt-2" :messages="$errors->get('location_details')" />
            </div>
        </div>

        <div class="flex space-x-4 mt-4">
            <div class="w-1/2">
                <x-input-label for="city" :value="__('City')" />
                <x-text-input id="city" name="city" type="text" class="mt-1 block w-full" required value="{{ old('city', $destination->city) }}" />
                <x-input-error class="mt-2" :messages="$errors->get('city')" />
            </div>

            <div class="w-1/2">
                <x-input-label for="country" :value="__('Country')" />
                <x-text-input id="country" name="country" type="text" class="mt-1 block w-full" required value="{{ old('country', $destination->country) }}" />
                <x-input-error class="mt-2" :messages="$errors->get('country')" />
            </div>
        </div>

        <div class="flex space-x-4 mt-4">
            <div class="w-1/2">
                <x-input-label for="activities" :value="__('Available Activities (Optional)')" />
                <textarea id="activities" name="activities" class="mt-1 block w-full dark:bg-gray-900 dark:text-gray-300">{{ old('activities', $destination->activities) }}</textarea>
                <x-input-error class="mt-2" :messages="$errors->get('activities')" />
            </div>

            <div class="w-1/2">
                <x-input-label for="images" :value="__('Replace/Add Images')" />
                <input id="images" name="images[]" type="file" class="mt-1 block w-full dark:bg-gray-900 dark:text-gray-300" multiple />
                <x-input-error class="mt-2" :messages="$errors->get('images')" />
            </div>
        </div> <!-- تأكد هذي تغلق الـ flex space-x-4 -->

        <div class="flex items-center justify-end mt-4 space-x-3">
            <a href="{{ route('destination.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-black dark:text-white uppercase tracking-widest hover:bg-gray-400 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition ease-in-out duration-150">
                {{ __('Cancel') }}
            </a>
            <x-primary-button>
                {{ __('Update') }}
            </x-primary-button>
        </div>
    </form>


<!-- عرض الصور -->
@foreach($destination->images as $image)
    <div class="relative group border rounded shadow overflow-hidden h-32">
        <img src="{{ asset('storage/' . $image->image_url) }}"
             class="w-full h-full object-cover" alt="Destination Image">

        <!-- حذف الصورة -->
        <form action="{{ route('destination-images.destroy', $image->id) }}" method="POST"
              class="absolute top-2 right-2 z-10">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="bg-red-600 text-white text-xs px-2 py-1 rounded hover:bg-red-700 shadow transition">
                ✕
            </button>
        </form>

        <!-- تعيين صورة رئيسية -->
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



                    {{--

                    <form method="POST" action="{{ route('destinations.update', $destination->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="flex space-x-4">
                            <div class="w-1/2">
                                <x-input-label for="name" :value="__('Destination Name')" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" required value="{{ old('name', $destination->name) }}" />
                                <x-input-error class="mt-2" :messages="$errors->get('name')" />
                            </div>
                            <div class="w-1/2">
                                <x-input-label for="description" :value="__('Description (Optional)')" />
                                <textarea id="description" name="description" class="mt-1 block w-full dark:bg-gray-900 dark:text-gray-300">{{ old('description', $destination->description) }}</textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('description')" />
                            </div>
                        </div>

                        <div class="flex space-x-4 mt-4">
                            <div class="w-1/2">
                                <x-input-label for="location_details" :value="__('Location Details')" />
                                <textarea id="location_details" name="location_details" class="mt-1 block w-full dark:bg-gray-900 dark:text-gray-300" required>{{ old('location_details', $destination->location_details) }}</textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('location_details')" />
                            </div>
                        </div>

                        <div class="flex space-x-4 mt-4">
                            <div class="w-1/2">
                                <x-input-label for="city" :value="__('City')" />
                                <x-text-input id="city" name="city" type="text" class="mt-1 block w-full" required value="{{ old('city', $destination->city) }}" />
                                <x-input-error class="mt-2" :messages="$errors->get('city')" />
                            </div>
                            <div class="w-1/2">
                                <x-input-label for="country" :value="__('Country')" />
                                <x-text-input id="country" name="country" type="text" class="mt-1 block w-full" required value="{{ old('country', $destination->country) }}" />
                                <x-input-error class="mt-2" :messages="$errors->get('country')" />
                            </div>
                        </div>

                        <div class="flex space-x-4 mt-4">
                            <div class="w-1/2">
                                <x-input-label for="activities" :value="__('Available Activities (Optional)')" />
                                <textarea id="activities" name="activities" class="mt-1 block w-full dark:bg-gray-900 dark:text-gray-300">{{ old('activities', $destination->activities) }}</textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('activities')" />
                            </div>
                            <div class="w-1/2">
                                <x-input-label for="images" :value="__('Replace/Add Images')" />
                                <input id="images" name="images[]" type="file" class="mt-1 block w-full dark:bg-gray-900 dark:text-gray-300" multiple onchange="showPrimarySelect(this)" />
                                <x-input-error class="mt-2" :messages="$errors->get('images')" />
                            </div>
                        </div>

                        <div id="primary-select-wrapper" class="mt-4 hidden">
                            <x-input-label for="primary_image_index" :value="__('Choose Primary Image')" />
                            <select name="primary_image_index" id="primary_image_index" class="mt-1 block w-full dark:bg-gray-900 dark:text-gray-300"></select>
                            <x-input-error class="mt-2" :messages="$errors->get('primary_image_index')" />
                        </div>

<!-- Current Images -->
<div class="mt-6">
    <p class="text-sm font-semibold mb-2">{{ __('Current Images:') }}</p>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach($destination->images as $image)
            <div class="relative group border rounded shadow overflow-hidden h-32">
                <img src="{{ asset('storage/' . $image->image_url) }}"
                     class="w-full h-full object-cover" alt="Destination Image">

                <!-- Delete Button -->
                <form action="{{ route('destination-images.destroy', $image->id) }}" method="POST"
                      class="absolute top-2 right-2 z-10">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="bg-red-600 text-white text-xs px-2 py-1 rounded hover:bg-red-700 shadow transition">
                        ✕
                    </button>
                </form>

                <!-- Set as Primary -->
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

<!-- Buttons -->
<div class="flex flex-wrap items-center justify-end mt-6 gap-4">
    <a href="{{ route('destination.index') }}"
       class="inline-flex items-center px-4 py-2 bg-gray-300 dark:bg-gray-600 text-black dark:text-white font-semibold rounded-md hover:bg-gray-400 dark:hover:bg-gray-500 shadow">
        {{ __('Cancel') }}
    </a>

    <x-primary-button>
        {{ __('Update Destination') }}
    </x-primary-button>
</div>

                    </form>
                </div>
            </div>
        </div>
    </div>
--}}
    <script>
        let allFiles = [];

        function showPrimarySelect(input) {
            const newFiles = Array.from(input.files);
            allFiles = newFiles;

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

            // Preserve the selected files after DOM update
            const dataTransfer = new DataTransfer();
            allFiles.forEach(file => dataTransfer.items.add(file));
            input.files = dataTransfer.files;
        }
    </script>
</x-app-layout>
