@php use App\Enums\UserRole; @endphp
@push('styles')
<link rel="stylesheet" href="{{asset('css/vehicles.css')}}">
<link rel="stylesheet" href="{{ asset('css/transport.css') }}">
@endpush
<x-app-layout>
    <div class="main-wrapper p-6 md:p-8">
        <form method="GET" action="{{ route('shift-templates.index') }}" class="flex flex-wrap gap-4 items-end mb-6">
            <div class="flex-1 min-w-[220px]">
                <label class="text-sm text-gray-600">Search</label>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Shift template name"
                    class="w-full border rounded-lg p-2"
                >
            </div>

            <div class="flex gap-2">
                <button class="bg-blue-600 text-white px-4 py-2 rounded-lg">Filter</button>
                <a href="{{ route('shift-templates.index') }}" class="px-4 py-2 border rounded-lg text-gray-600">Reset</a>
            </div>
        </form>

        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="p-6 border-b border-gray-200 flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Shift Templates</h2>
                    <p class="text-gray-500 mt-1">Manage the reusable shift templates.</p>
                </div>
                   

                <button class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700"
        id="popup-btn">
    Create Shift
</button>
               
            </div>

            @if (session('success'))
                <div class="m-4 px-4 py-3 bg-green-100 text-green-800 rounded">
                    {{ session('success') }}
                </div>
            @endif

             @if (session('error'))
              <div class="m-4 px-4 py-3 bg-red-100 text-red-800 rounded">
            {{ session('error') }}
               </div>
               @endif

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Days</th>
                             
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">   <span class="sr-only">Actions</span> </th>

                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($shiftTemplates as $template)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $template->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ \Carbon\Carbon::parse($template->start_time)->format('H:i') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ \Carbon\Carbon::parse($template->end_time)->format('H:i') }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ implode(', ', $template->days_of_week ?? []) }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                          <form action="{{ route('shift-templates.destroy', $template->id) }}"  method="POST" class="inline"
                                        onsubmit="return confirm('Are you sure you want to delete this template?');">
                                        @csrf
                                        @method('DELETE')
                                         <button type="submit" class="p-1">
                                         <img src="{{ asset('images/icons/delete.png') }}" alt="Details Icon" class="w-6 h-6 align-middle">
                                         </button>
                                    </form>
                                        
                                    </td>
                              
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-gray-500">No shift templates found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4">
                {{ $shiftTemplates->links() }}
            </div>
        </div>
    </div>



      <!-- Add Shift Popup Overlay -->
<div id="popup-overlay" style="display: {{ $errors->any() ? 'flex' : 'none' }};">
    <div class="popup">
        <h2>Add Shift Template</h2>

        <form action="{{ route('shift-templates.store') }}" method="POST">
            @csrf

            @if ($errors->any())
                <div class="mb-4 px-4 py-3 bg-red-100 text-red-800 rounded">
                    <ul class="list-disc list-inside" style="display:flex;flex-direction:column">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <x-input-label for="name" value="Shift Name"/>
            <x-text-input id="name" type="text" name="name" required placeholder="Enter shift name"
                value="{{ old('name') }}"/>

            <x-input-label for="start_time" value="Start Time"/>
            <input type="time" name="start_time" id="start_time"
                value="{{ old('start_time') }}" required>

            <x-input-label for="end_time" value="End Time"/>
            <input type="time" name="end_time" id="end_time"
                value="{{ old('end_time') }}" required>

        
<x-input-label value="Days of Week"/>

<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-2">
    @php
        $days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
    @endphp

    @foreach($days as $day)
        <div class="border rounded-lg p-3 hover:bg-gray-50 transition">
            <label class="flex items-center gap-2 cursor-pointer">
                
                <input type="checkbox"
                       name="days_of_week[]"
                       value="{{ $day }}"
                       style="width:18px;height:18px;flex:none;display:inline-block;"
                       {{ in_array($day, old('days_of_week', [])) ? 'checked' : '' }}>

                <span class="text-sm text-gray-700">
                    {{ $day }}
                </span>

            </label>
        </div>
    @endforeach
</div>


            <div class="popup-buttons" style="margin-top:15px;">
                <button type="submit" class="btn btn-primary">Save</button>
                <button type="button" id="close-popup" style="background:#ccc;">Cancel</button>
            </div>
        </form>
    </div>
</div>

</x-app-layout>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const popupOverlay = document.getElementById('popup-overlay');
    const addServiceBtn = document.getElementById('popup-btn');
    const closePopupBtn = document.getElementById('close-popup');

    addServiceBtn?.addEventListener('click', () => {
        popupOverlay.style.display = 'flex';
    });

    closePopupBtn?.addEventListener('click', () => {
        popupOverlay.style.display = 'none';
    });

    window.addEventListener('click', e => {
        if (e.target === popupOverlay) {
            popupOverlay.style.display = 'none';
        }
    });
});
</script>
