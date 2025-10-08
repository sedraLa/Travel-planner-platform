@push('styles')
<link rel="stylesheet" href="{{asset('css/vehicles.css')}}">
<link rel="stylesheet" href="{{ asset('css/transport.css') }}">
@endpush
<x-app-layout>
    <div class="main-wrapper p-6 md:p-8"> 

        {{-- Add new driver button--}}
        <div class="flex justify-end mb-6">
            <a href="{{ route('drivers.create') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-300 ease-in-out flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add New Driver
            </a>
        </div>

        {{--Success messages--}}
        @if (session('success'))
            <div class="mb-6 px-4 py-3 bg-green-100 border border-green-200 text-green-800 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        {{--Drivers list --}}
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-2xl font-bold text-gray-800">Driver List</h2>
                <p class="text-gray-500 mt-1">A list of all the drivers in your system.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Name</th>
                                <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Email</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Contact</th>
                                <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Address</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date of Hire</th>
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($drivers as $driver)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $driver->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $driver->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $driver->phone }}</div>
                                    
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $driver->address ?? 'No address' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($driver->status == 'active')
                                        <span
                                            class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Available
                                        </span>
                                    @else
                                        <span
                                            class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Unavailable
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $driver->date_of_hire ? \Carbon\Carbon::parse($driver->date_of_hire)->format('d-m-Y') : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('drivers.edit', $driver->id) }}"
                                        class="text-indigo-600 hover:text-indigo-900 mr-4">Edit</a>
                                    <form action="{{ route('drivers.destroy', $driver->id) }}" method="POST" class="inline"
                                        onsubmit="return confirm('Are you sure you want to delete this driver?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                    </form>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('drivers.show', $driver->id) }}"
                                        class="text-green-600 hover:green-600 -900 mr-4 text-sm">View Reservations</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    No drivers found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>