<x-app-layout>
    @push('styles')
        <style>
            .details-container {
                max-width: 480px;
                margin: 2rem auto;
                padding: 2rem;
                background-color: rgba(255, 255, 255, 0.65);
                backdrop-filter: blur(12px);
                -webkit-backdrop-filter: blur(12px);
                border-radius: 1rem;
                box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
                border: 1px solid rgba(255, 255, 255, 0.18);
            }

            .details-container .page-title {
                font-size: 1.5rem;
                font-weight: 600;
                text-align: center;
                margin-bottom: 1.5rem;
                color: #1f2937;
            }
        </style>
    @endpush


    <div class="absolute inset-0 -z-10">
        <img src="{{ asset('images/background.jpg') }}" alt="background" class="w-full h-full object-cover">

    </div>

    <div class="py-12">
        <div class="details-container">
            <h1 class="page-title">Activity Details</h1>

            @if($activity->image)
                <div class="mb-6">
                    <img src="{{ asset('storage/' . $activity->image) }}" alt="{{ $activity->name }}"
                        class="w-full h-auto rounded-lg shadow-md">
                </div>
            @endif


            <div class="space-y-5">


                <div class="text-center">
                    <h2 class="text-2xl font-bold text-gray-900 inline-block mr-2">{{ $activity->name }}</h2>
                    <span
                        class="inline-block align-middle px-2.5 py-0.5 text-xs font-semibold rounded-full {{ $activity->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $activity->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>

                <div class="text-center border-t border-b border-gray-200 py-4">
                    <p class="text-sm text-gray-600 mb-1">Description</p>
                    <p class="text-base text-gray-800">{{ $activity->description ?? 'N/A' }}</p>
                </div>

                <div class="flex justify-between text-center px-4">
                    <div>
                        <p class="text-sm text-gray-600">Price</p>
                        <p class="font-semibold text-lg text-gray-900">${{ number_format($activity->price, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Duration</p>
                        <p class="font-semibold text-lg text-gray-900">{{ $activity->duration }}
                            {{ $activity->duration_unit }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Category</p>
                        <p class="font-semibold text-lg text-gray-900">{{ ucfirst(strtolower($activity->category)) }}
                        </p>
                    </div>
                </div>



            </div>


            <div class="pt-8 text-center">
                <a href="{{ route('activities.index') }}"
                    class="inline-block bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded-lg transition duration-300">
                    Back to Activities
                </a>
            </div>
        </div>
    </div>
</x-app-layout>