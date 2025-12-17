@push('styles')
<link rel="stylesheet" href="{{asset('css/vehicles.css')}}">
<link rel="stylesheet" href="{{ asset('css/transport.css') }}">
@endpush
<x-app-layout>

    <div class="main-wrapper p-6 md:p-8"> 

        {{--Success messages--}}
     @if (session('success'))
    <div class="fixed top-4 left-1/2 transform -translate-x-1/2 mb-6 px-4 py-3 bg-green-100 border border-green-200 text-green-800 rounded-lg z-50">
        {{ session('success') }}
    </div>
@endif



          {{-- Search Form  --}}
        <form class="search-form" method="GET" action="{{route('drivers.index')}}"  style="margin-top:50px; "">
            <h1>Search  for a driver by Name or Category</h1>
            <div class="search-container">
                <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                </svg>
                <input type="search" id="default-search" name="search" class="search-input" placeholder="Search destinations..." required />
                <button type="submit" class="search-button">Search</button>
            </div>
        </form>


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
                                 First Name</th>
                                <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Last Name</th>
                                <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Email</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Contact</th>
                                <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Country</th>
                                <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Address</th>
                                 <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                experience</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                License Category</th>

                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    
                                 Photo of license</th>
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
                                            <div class="text-sm font-medium text-gray-900"> {{ $driver->user->name ?? '—' }}</div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $driver->user->last_name  ?? '—'  }}</div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $driver->user->email ?? '—' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $driver->user->phone_number ?? '—' }}</div>
                                    
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $driver->user->country ?? '—' }}</div>
                                    
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $driver->address ?? 'No address' }}</div>
                                </td>

                                 <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $driver-> experience?? 'No address' }}</div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap  text-center">
                                    <div class="text-sm font-medium text-gray-900">{{ $driver->license_category }}</div>
                                    
                                </td>


                                 <td class="px-6 py-4 whitespace-nowrap text-center">
                               <!-- زر عرض الصورة -->
                         <button 
                              onclick="document.getElementById('license-modal-{{ $driver->id }}').classList.remove('hidden')"
                              class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm font-medium">
                                      View License
                          </button>

                                            <!-- Modal -->
                           <div id="license-modal-{{ $driver->id }}" 
                            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
                            <div class="bg-white p-4 rounded shadow-lg relative max-w-lg w-full">
                                          <!-- زر إغلاق -->
                             <button 
                                     onclick="document.getElementById('license-modal-{{ $driver->id }}').classList.add('hidden')"
                                     class="absolute top-2 right-2 text-gray-600 hover:text-gray-900 font-bold text-2xl">
                                     &times;
                              </button>
                                         <!-- الصورة -->
                                   @if($driver->license_image)
                     <img src="{{ asset('storage/' . $driver->license_image) }}" 
                      alt="License Photo" class="w-full h-auto rounded">

                        @else
                         <p class="text-center text-gray-500">No license photo available</p>
                        @endif

                       </div>
                     </div>
                    </td>


                            <td class="px-6 py-4 whitespace-nowrap">
                                 <form method="POST" action="{{ route('drivers.updateStatus', $driver->id) }}">
                                          @csrf
                                          @method('PATCH')

                                    <div x-data="{ status: '{{ $driver->status ?? 'pending' }}' }" 
                                      class="flex items-center space-x-2">
            
                                      <select name="status" x-model="status"
                                       @if($driver->status === 'approved') disabled @endif
                                         class="px-4 py-2 text-sm font-semibold rounded-full appearance-none border-none focus:outline-none transition-colors duration-200"
                                         {{ $driver->status === 'approved'
                            ? 'bg-gray-200 text-gray-500 cursor-not-allowed'
                            : '' }}"
                                        
                                         :class="{
                                             'bg-orange-100 text-orange-800': status === 'pending',
                                             'bg-green-100 text-green-800': status === 'approved',
                                             'bg-red-100 text-red-800': status === 'rejected'
                                                  }">
                                             <option value="pending">Pending</option>
                                             <option value="approved">Approved</option>
                                             <option value="rejected">Rejected</option>
                                        </select>

                                           <button type="submit"
                                              @if($driver->status === 'approved') disabled @endif
                                                    class="px-2 py-1 text-sm font-medium rounded
                                                    {{ $driver->status === 'approved'
                                                    ? 'bg-gray-400 text-white cursor-not-allowed'
                                                     : 'bg-blue-600 text-white hover:bg-blue-700' }}">
                                                                  Confirm
                                             </button>
                                     </div>
                                </form>
                            </td>

                                
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $driver->date_of_hire ? \Carbon\Carbon::parse($driver->date_of_hire)->format('d-m-Y') : 'N/A' }}
                                </td>
                                

                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">

                                   @if($driver->status !== 'approved')
                                        <button
                                           class="text-gray-400 cursor-not-allowed mr-4 text-sm font-medium"disabled>
                                                  Delete
                                        </button>
                                    @else
                                  
                                    <form action="{{ route('drivers.destroy', $driver->id) }}" method="POST" class="inline"
                                        onsubmit="return confirm('Are you sure you want to delete this driver?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900  mr-4">Delete</button>
                                    </form>
                                    @endif
                                    
                                       @if($driver->status === 'approved')
                                        <a class="text-green-600 hover:text-green-900 mr-4 text-sm font-medium" 
                                          href="{{ route('drivers.show', $driver->id) }}">
                                                 Completed Reservations </a>

                                        @else
                                          <button class="text-gray-400 cursor-not-allowed mr-4 text-sm font-medium" disabled>
                                             Completed Reservations
                                          </button>
                                        @endif

                                        @if($driver->status === 'approved')
                                        <a class="text-yellow-600 hover:text-yellow-900 mr-4 text-sm font-medium" 
                                          href="{{ route('admin.bookings.pending', $driver->id) }}">
                                                Pending Reservations </a>

                                        @else
                                          <button class="text-gray-400 cursor-not-allowed mr-4 text-sm font-medium" disabled>
                                             Pending Reservations
                                          </button>
                                        @endif





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






<style>
/* اجعل الـ div المحتوي flex بشكل افقي */
td .inline-block {
    display: flex;
    align-items: center;
    gap: 0.5rem; /* مسافة بين select والزر */
}

/* اضبط الـ select */
td select {
    min-width: 120px; /* أكبر من قبل */
    padding: 0.5rem 1rem;
    text-align: center;
    border-radius: 9999px; /* rounded-full */
    border: none;
    appearance: none;
    cursor: pointer;
    font-weight: 600;
}

/* اضبط الزر */
td button {
    flex-shrink: 0; /* لا يقلص عند ضغط الجدول */
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem; /* text-sm */
    font-weight: 500;
    border-radius: 0.375rem;
}

/* ألوان خيارات الـ select */
td select option {
    background-color: white;
    color: #333;
}
</style>
