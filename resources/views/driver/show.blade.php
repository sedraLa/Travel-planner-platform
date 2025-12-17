@push('styles')
<link rel="stylesheet" href="{{asset('css/vehicles.css')}}">
<link rel="stylesheet" href="{{ asset('css/transport.css') }}">
@endpush
<x-app-layout>
    <div class="main-wrapper p-6 md:p-8"> 

        

        {{--Success messages--}}
        @if (session('success'))
            <div class="mb-6 px-4 py-3 bg-green-100 border border-green-200 text-green-800 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        {{--Reservation list --}}
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="p-6 border-b border-gray-200">

                 @if (Auth::user()->role === \App\Enums\UserRole::DRIVER->value)
                 <h2 class="text-2xl font-bold text-gray-800">  My completed Reservations</h2>
                <p class="text-gray-500 mt-1">A list of all my reservations.</p>
                  @else
                <h2 class="text-2xl font-bold text-gray-800">Reservations {{ $driver->user->name }} </h2>
                <p class="text-gray-500 mt-1">A list of all the reservations in your system for a driver.</p>
                 @endif
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                              
                                <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Client Name</th>
                                <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                vehicle</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Pickup Location</th>
                                <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Dropoff Location</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Pickup DateTime </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Passengers</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total Price </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status</th>

                            
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($reservations as $reservation)
                    <tr>
                       
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $reservation->user->name?? 'N/A'}}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $reservation->vehicle->car_model ?? 'N/A'}}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $reservation->pickup_location }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $reservation->dropoff_location }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ \Carbon\Carbon::parse($reservation->pickup_datetime)->format('d-m-Y H:i') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $reservation->passengers }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">${{ $reservation->total_price }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">Completetd</td>
                     
                                     
                      

                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">No reservations found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
     </div>
  </div>
 </div>
</x-app-layout>