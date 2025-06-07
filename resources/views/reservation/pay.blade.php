<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Complete Payment for your Reservation') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
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
                <p class="mb-4 text-gray-700">Total to pay: ${{ $reservation->total_price }}</p>

                <form action="{{ route('payment.paypal', $reservation->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        Pay with PayPal
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
