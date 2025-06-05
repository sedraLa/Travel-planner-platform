<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Make a Reservation at') }} {{ $hotel->name }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('reservations.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="hotel_id" value="{{ $hotel->id }}">

                    <!-- Guest Count -->
                    <div class="mb-4">
                        <label class="block text-gray-700">Number of Guests</label>
                        <input type="number" name="guest_count" class="form-input w-full" required>
                    </div>

                    <!-- Rooms Count -->
                    <div class="mb-4">
                        <label class="block text-gray-700">Number of Rooms</label>
                        <input type="number" name="rooms_count" class="form-input w-full" required>
                    </div>

                    <!-- Check-in Date -->
                    <div class="mb-4">
                        <label class="block text-gray-700">Check-In Date</label>
                        <input type="date" name="check_in_date" class="form-input w-full" required>
                    </div>

                    <!-- Check-out Date -->
                    <div class="mb-4">
                        <label class="block text-gray-700">Check-Out Date</label>
                        <input type="date" name="check_out_date" class="form-input w-full" required>
                    </div>

                    <!-- Name -->
                    <div class="mb-4">
                        <label class="block text-gray-700">Full Name</label>
                        <input type="text" name="name" value="{{ Auth::user()->name ?? '' }}" class="form-input w-full"
                            required>
                    </div>

                    <!-- Email -->
                    <div class="mb-4">
                        <label class="block text-gray-700">Email</label>
                        <input type="email" name="email" value="{{ Auth::user()->email ?? '' }}"
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