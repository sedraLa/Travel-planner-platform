@push('styles')
<link rel="stylesheet" href="{{asset('css/vehicles.css')}}">
<link rel="stylesheet" href="{{ asset('css/transport.css') }}">
@endpush
<x-app-layout>

    <div class="main-wrapper p-6 md:p-8"> 

          {{-- Search Form  --}}
          <form method="GET" action="{{ route('guide-applications.index') }}" class="flex flex-wrap gap-4 items-end mb-6">
            {{-- Keyword --}}
            <div class="flex-1 min-w-[200px]">
                <label class="text-sm text-gray-600">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Name,  Country, Specializations, languages" class="w-full border rounded-lg p-2">
            </div>
        
            
        
            
        
            {{-- Country --}}
            <div>
                <label class="text-sm text-gray-600">Country</label>
                <input type="text" name="country" value="{{ request('country') }}" placeholder="Country" class="border rounded-lg p-2">
            </div>

             {{-- languages --}}
            <div>
                <label class="text-sm text-gray-600">Languages</label>
                <input type="text" name="languages" value="{{ request('languages') }}" placeholder="Languages" class="border rounded-lg p-2">
            </div>

            <div>
    
</div>
        
            {{-- Actions --}}
            <div class="flex gap-2">
                <button class="bg-blue-600 text-white px-4 py-2 rounded-lg">Filter</button>
                <a href="{{ route('guide-applications.index') }}" class="px-4 py-2 border rounded-lg text-gray-600">Reset</a>
            </div>
        </form>
        


        {{--Guides list --}}
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                
                <h2 class="text-2xl font-bold text-gray-800">Guides Requests List</h2>
                <p class="text-gray-500 mt-1">A list of all the Guides who  send an application request</p>
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
                                 Full Name</th>
                                <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Country</th>
                                <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Languages</th>
                              
                                <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status</th>
                            
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($guides as $guide)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900"> {{ $guide->user->name ?? '—' }} {{ $guide->user->last_name  ?? '—'  }}</div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $guide->user->country ?? '—' }}</div>
                                    
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap  text-center" style="width:160px;">
                                    <div class="text-sm font-medium text-gray-900" style="margin-right:50px;">{{ $guide->languages }}</div>
                                    
                                </td>

                               

                    

                                        

                    <!--Update Guide status-->
                            <td class="px-6 py-4 whitespace-nowrap" style="width:50px;">
                                 <form method="POST" action="{{ route('guide.updateStatus', $guide->id) }}">
                                          @csrf
                                          @method('PATCH')

                                    <div x-data="{ status: '{{ $guide->status ?? 'pending' }}' }" 
                                      class="flex items-center space-x-2">
                                <!--disable select-->
                                      <select name="status" x-model="status"
                                       @if($guide->status === 'approved') disabled @endif
                                         class="px-4 py-2 text-sm font-semibold rounded-full appearance-none border-none focus:outline-none transition-colors duration-200"
                                         {{ $guide->status === 'approved'
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

                                           <button type="submit" style="margin-left:30px;margin-bottom:12px;"
                                       
                                              @if($guide->status === 'approved') disabled @endif
                                                    class="px-2 py-1 text-sm font-medium rounded
                                                    {{ $guide->status === 'approved'
                                                    ? 'bg-gray-400 text-white cursor-not-allowed'
                                                     : 'bg-blue-600 text-white hover:bg-blue-700' }}">
                                                                  Confirm
                                             </button>
                                     </div>
                                </form>
                                
                            </td>
                    
                            

                             <td class="px-6 py-4 whitespace-nowrap  text-center">
                                  <a href="{{ route('guide-applications.show', $guide->id) }}">
                                        <div class="inline-flex items-center gap-2"  style="margin-bottom:10px;">
                                            <img src="{{ asset('images/icons/details.png') }}" alt="Details Icon" class="w-6 h-6 align-middle">
                                             <button class="order-btn edit-btn px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                                  DETAILS
                                                    </button>
                                        </div>
                                    </a>
                                   
                             </td>


            
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    No guides found.
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