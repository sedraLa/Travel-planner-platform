
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            
            {{ __('Create New Destination') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-7xl">

                                   @if ($errors->any())
                                           <div class="mb-4 text-red-600">
                                              <ul class="list-disc list-inside">
                                               @foreach ($errors->all() as $error)
                                                     <li>{{ $error }}</li>
                                                   @endforeach
                                                    </ul>
                                                        </div>
                                                               @endif
                    <form method="post" action="{{ route('destinations.store') }}" enctype="multipart/form-data">
                        @csrf

                        <!-- Destination Name & Description -->
                        <div class="flex space-x-4">
                            <div class="w-1/2">
                                <x-input-label for="name" :value="__('Destination Name')" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" required autofocus />
                                <x-input-error class="mt-2" :messages="$errors->get('name')" />
                            </div>
                            <div class="w-1/2">
                                <x-input-label for="description" :value="__('Description')" />
                                <textarea id="description" name="description" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm"></textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('description')" />
                            </div>
                        </div>

                        <!-- Location & Weather -->
                        <div class="flex space-x-4 mt-4">
                            <div class="w-1/2">
                                <x-input-label for="location_details" :value="__('Location Details')" />
                                <textarea id="location_details" name="location_details"" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm" required></textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('location_details')" />
                            </div>
                            <div class="w-1/2">
                                <x-input-label for="weather_info" :value="__('Weather Conditions')" />
                                <textarea id="weather_info" name="weather_info" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm" required></textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('weather_info')" />
                            </div>
                        </div>
                                                                                     

                         <div class="flex space-x-4 mt-4">
                                         <div class="w-1/2">
                                     <x-input-label for="city" :value="__('City')" />
                                        <x-text-input id="city" name="city" type="text" class="mt-1 block w-full" required />
                                       <x-input-error class="mt-2" :messages="$errors->get('city')" />
                                          </div>
                                           <div class="w-1/2">
                                             <x-input-label for="country" :value="__('Country')" />
                                       <x-text-input id="country" name="country" type="text" class="mt-1 block w-full" required />
                                        <x-input-error class="mt-2" :messages="$errors->get('country')" />
                                               </div>
                                          </div>
                        <!-- Activities & Images -->
                        <div class="flex space-x-4 mt-4">
                            <div class="w-1/2">
                                <x-input-label for="activities" :value="__('Available Activities')" />
                                <textarea id="activities" name="activities" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm" required></textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('activities')" />
                            </div>

                            <!-- Images Upload -->
                            <div class="w-1/2">
                                <x-input-label for="images" :value="__('Images (Optional)')" />
                                <input id="images" name="images[]" type="file" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm" multiple onchange="showPrimarySelect(this)" />
                                <x-input-error class="mt-2" :messages="$errors->get('images')" />
                            </div>
                        </div>

                        <!-- Primary Image -->
                        <div id="primary-select-wrapper" class="mt-4 hidden">
                            <x-input-label for="primary_image_index" :value="__('Choose Primary Image')" />
                            <select name="primary_image_index" id="primary_image_index" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm"></select>
                            <x-input-error class="mt-2" :messages="$errors->get('primary_image_index')" />
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button>{{ __('Create Destination') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
function showPrimarySelect(input) {
    const files = input.files;
    const select = document.getElementById('primary_image_index');
    const wrapper = document.getElementById('primary-select-wrapper');

    if (files.length > 0) {
        select.innerHTML = ''; // إزالة الخيارات السابقة
        wrapper.classList.remove('hidden'); // إظهار القائمة

        for (let i = 0; i < files.length; i++) {
            const option = document.createElement('option');
            option.value = i;
            option.textContent = files[i].name;
            select.appendChild(option);
        }
    } else {
        wrapper.classList.add('hidden'); // إخفاء القائمة إذا ما في صور
    }
}
</script>