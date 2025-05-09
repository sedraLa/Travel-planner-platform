<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            <x-back-button></x-back-button>
            {{ __('Edit Destination') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-messages />
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-7xl">
                    <form method="POST" action="{{ route('destinations.update', $destination->id) }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Name & City -->
                        <div class="flex space-x-4">
                            <div class="w-1/2">
                                <x-input-label for="name" :value="__('Destination Name')" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                    value="{{ old('name', $destination->name) }}" required autofocus />
                                <x-input-error class="mt-2" :messages="$errors->get('name')" />
                            </div>
                            <div class="w-1/2">
                                <x-input-label for="city" :value="__('City')" />
                                <x-text-input id="city" name="city" type="text" class="mt-1 block w-full"
                                    value="{{ old('city', $destination->city) }}" required />
                                <x-input-error class="mt-2" :messages="$errors->get('city')" />
                            </div>
                        </div>

                        <!-- Country & Location Details -->
                        <div class="flex space-x-4 mt-4">
                            <div class="w-1/2">
                                <x-input-label for="country" :value="__('Country')" />
                                <x-text-input id="country" name="country" type="text" class="mt-1 block w-full"
                                    value="{{ old('country', $destination->country) }}" required />
                                <x-input-error class="mt-2" :messages="$errors->get('country')" />
                            </div>
                            <div class="w-1/2">
                                <x-input-label for="location_details" :value="__('Location Details')" />
                                <textarea id="location_details" name="location_details"
                                    class="mt-1 block w-full rounded-md shadow-sm dark:bg-gray-900 dark:text-gray-300"
                                    required>{{ old('location_details', $destination->location_details) }}</textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('location_details')" />
                            </div>
                        </div>

                        <!-- Description & Activities -->
                        <div class="flex space-x-4 mt-4">
                            <div class="w-1/2">
                                <x-input-label for="description" :value="__('Description')" />
                                <textarea id="description" name="description"
                                    class="mt-1 block w-full rounded-md shadow-sm dark:bg-gray-900 dark:text-gray-300">{{ old('description', $destination->description) }}</textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('description')" />
                            </div>
                            <div class="w-1/2">
                                <x-input-label for="activities" :value="__('Activities')" />
                                <textarea id="activities" name="activities"
                                    class="mt-1 block w-full rounded-md shadow-sm dark:bg-gray-900 dark:text-gray-300">{{ old('activities', $destination->activities) }}</textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('activities')" />
                            </div>
                        </div>
                        <!-- Weather Info -->
                        <div class="mt-4">
                            <x-input-label for="weather_info" :value="__('Weather Info')" />
                            <textarea id="weather_info" name="weather_info"
                                class="mt-1 block w-full rounded-md shadow-sm dark:bg-gray-900 dark:text-gray-300"
                                required>{{ old('weather_info', $destination->weather_info) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('weather_info')" />
                        </div>

                        <!-- Images Upload -->
                        <div class="mt-6">
                            <x-input-label for="images" :value="__('Upload New Images')" />
                            <input id="images" name="images[]" type="file" multiple
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm" />
                            <x-input-error class="mt-2" :messages="$errors->get('images')" />
                        </div>

                        <!-- Current Images Display -->
                        <div class="mt-6">
                            <p class="text-sm font-semibold mb-2">{{ __('Current Images:') }}</p>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                @foreach($destination->images as $image)
                                    <div class="relative">
                                        <img src="{{ asset('storage/' . $image->image_url) }}"
                                            class="rounded shadow w-full h-32 object-cover" alt="Destination Image">

                                        @if(!$image->is_primary)
                                            <form method="POST" action="{{ route('images.destroy', $image->id) }}"
                                                class="absolute top-1 right-1">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm('Are you sure?')"
                                                    class="bg-red-600 text-white text-xs px-2 py-1 rounded">X</button>
                                            </form>
                                        @endif

                                        <div
                                            class="text-xs text-center mt-1 {{ $image->is_primary ? 'text-green-600 font-bold' : '' }}">
                                            {{ $image->is_primary ? __('Primary') : '' }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Submit -->
                        <div class="mt-6 flex justify-end">
                            <x-primary-button>{{ __('Save Changes') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>