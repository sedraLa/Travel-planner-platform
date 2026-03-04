<x-app-layout>
    @push('styles')
<link rel="stylesheet" href="{{asset('css/vehicles.css')}}">
<link rel="stylesheet" href="{{ asset('css/transport.css') }}">
@endpush
    <div class="max-w-3xl mx-auto py-16 text-center">
        <h1 class="text-3xl font-bold mb-4">We are looking for a vehicle</h1>
        <p class="text-gray-600 mb-8">Please wait while we contact available drivers.</p>
        <div class="w-16 h-16 border-4 border-blue-500 border-t-transparent rounded-full animate-spin mx-auto"></div>
    </div>

    <script>
        const statusUrl = @json(route('vehicle.searching.status', $reservation));
        setInterval(async () => {
            const response = await fetch(statusUrl);
            const data = await response.json();

            if (data.redirect_url) {
                window.location.href = data.redirect_url;
            }
        }, 3000);
    </script>
</x-app-layout>