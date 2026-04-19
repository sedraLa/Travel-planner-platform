<x-app-layout>
    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/transport.css') }}">
    <link rel="stylesheet" href="{{ asset('css/vehicles.css') }}">
@endpush
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
           {{ __('Make a Reservation at') }} {{ $activity->name }} in {{ $activity->destination->name }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

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

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('activity.reservations.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="activity_id" value="{{ $activity->id }}">
                    <div class="mb-6">
                        <p class="font-semibold text-xl text-indigo-900 leading-tight">
                            Price per person in this  Activity is {{$activity->price}}</p>
                    </div>


                    <!-- Guest Count -->
                    <div class="mb-4">
                        <label class="block text-gray-700">Number of Guests</label>
                        <input type="number" name="participants_count" class="form-input w-full" required>
                    </div>

                    

                    <!-- Check-in Date -->
                    <div class="mb-4">
                        <label class="block text-gray-700">activity_date</label>
                        <input type="date" name="activity_date" class="form-input w-full" required>
                    </div>

                  

                    <!-- Name -->
                    <div class="mb-4">
                        <label class="block text-gray-700">First Name</label>
                        <input type="text" name="name" value="{{ Auth::user()->name ?? '' }}" class="form-input w-full"
                            required>
                    </div>
                    <!-- last_Name -->
                    <div class="mb-4">
                        <label class="block text-gray-700">Last Name</label>
                        <input type="text" name="last_name" value="{{ Auth::user()->last_name ?? '' }}"
                            class="form-input w-full" required>
                    </div>
                    <!-- country -->
                    <div class="mb-4">
                        <label class="block text-gray-700">Country</label>
                        <input type="text" name="country" value="{{ Auth::user()->country ?? '' }}"
                            class="form-input w-full" required>
                    </div>


                    <!-- Email -->
                    <div class="mb-4">
                        <label class="block text-gray-700">Email</label>
                        <input type="email" name="email" value="{{ Auth::user()->email ?? '' }}"
                            class="form-input w-full" required>
                    </div>
                    <!-- phone_number -->
                    <div class="mb-4">
                        <label class="block text-gray-700">Phone Number</label>
                        <input type="text" name="phone_number" value="{{ Auth::user()->phone_number ?? '' }}"
                            class="form-input w-full" required>
                    </div>


                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        Confirm Reservation
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
