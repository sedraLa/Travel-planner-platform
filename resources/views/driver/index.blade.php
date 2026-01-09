@push('styles')
<link rel="stylesheet" href="{{asset('css/vehicles.css')}}">
<link rel="stylesheet" href="{{ asset('css/transport.css') }}">
@endpush
<x-app-layout>

    <div class="main-wrapper p-6 md:p-8"> 

          {{-- Search Form  --}}
          <form method="GET" action="{{ route('drivers.index') }}" class="flex flex-wrap gap-4 items-end mb-6">
            {{-- Keyword --}}
            <div class="flex-1 min-w-[200px]">
                <label class="text-sm text-gray-600">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, Email, Category, Status" class="w-full border rounded-lg p-2">
            </div>
        
            {{-- Status --}}
            <div>
                <label class="text-sm text-gray-600">Status</label>
                <select name="status" class="border rounded-lg p-2" style="margin-bottom:0">
                    <option value="">All</option>
                    <option value="pending" @selected(request('status')=='pending')>Pending</option>
                    <option value="approved" @selected(request('status')=='approved')>Approved</option>
                    <option value="rejected" @selected(request('status')=='rejected')>Rejected</option>
                </select>
            </div>
        
            {{-- License Category --}}
            <div>
                <label class="text-sm text-gray-600">License Category</label>
                <select name="license_category" class="border rounded-lg p-2" style="margin-bottom:0">
                    <option value="">All</option>
                    <option value="A" @selected(request('license_category')=='A')>A</option>
                    <option value="B" @selected(request('license_category')=='B')>B</option>
                </select>
            </div>
        
            {{-- Country --}}
            <div>
                <label class="text-sm text-gray-600">Country</label>
                <input type="text" name="country" value="{{ request('country') }}" placeholder="Country" class="border rounded-lg p-2">
            </div>
        
            {{-- Actions --}}
            <div class="flex gap-2">
                <button class="bg-blue-600 text-white px-4 py-2 rounded-lg">Filter</button>
                <a href="{{ route('drivers.index') }}" class="px-4 py-2 border rounded-lg text-gray-600">Reset</a>
            </div>
        </form>
        


        {{--Drivers list --}}
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                
                <h2 class="text-2xl font-bold text-gray-800">Driver List</h2>
                <p class="text-gray-500 mt-1">A list of all the drivers in your system.</p>
                                {{--Success messages--}}
                                @if (session('success'))
                                <div class="mb-4 px-4 py-3 bg-green-100 text-green-800 rounded">
                                    {{ session('success') }}
                                </div>
                            @endif
                
                 @if(session('error'))
                 <div class="mb-4 p-4 bg-red-100 text-red-700 rounded text-sm">
                    <div class="mb-1">{{ session('error') }}</div>
                 </div>
                @endif
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

                    <!--Update driver status-->
                            <td class="px-6 py-4 whitespace-nowrap">
                                 <form method="POST" action="{{ route('drivers.updateStatus', $driver->id) }}">
                                          @csrf
                                          @method('PATCH')

                                    <div x-data="{ status: '{{ $driver->status ?? 'pending' }}' }" 
                                      class="flex items-center space-x-2">
                                <!--disable select-->
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
                                           {{--disable submit--}}
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

                                <!-- date of hire-->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $driver->date_of_hire ? \Carbon\Carbon::parse($driver->date_of_hire)->format('d-m-Y') : 'N/A' }}
                                </td>
                                

                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <!--delete driver button-->
                                   @if($driver->status !== 'approved')
                                        <button
                                           class="text-gray-400 cursor-not-allowed mr-4 text-sm font-medium"disabled>
                                                  Delete
                                        </button>
                                    @else
                                  <!--delete driver-->
                                    <form action="{{ route('drivers.destroy', $driver->id) }}" method="POST" class="inline"
                                        onsubmit="return confirm('Are you sure you want to delete this driver?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900  mr-4">Delete</button>
                                    </form>
                                    @endif

                                    <!--Completed reservations button-->
                                       @if($driver->status === 'approved')
                                        <a class="text-green-600 hover:text-green-900 mr-4 text-sm font-medium" 
                                          href="{{ route('admin.bookings.completed', $driver->id) }}">
                                                 Completed Reservations </a>
                                        @else
                                          <button class="text-gray-400 cursor-not-allowed mr-4 text-sm font-medium" disabled>
                                             Completed Reservations
                                          </button>
                                        @endif
                                          <!--pending reservations button-->
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