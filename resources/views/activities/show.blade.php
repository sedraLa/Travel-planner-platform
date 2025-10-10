<td class="px-6 py-4 whitespace-nowrap text-center">
    <!-- زر عرض الصورة -->
    <button onclick="document.getElementById('activity-modal-{{ $activity->id }}').classList.remove('hidden')"
        class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm font-medium">
        View Activity
    </button>

    <!-- Modal -->
    <div id="activity-modal-{{ $activity->id }}"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white p-4 rounded shadow-lg relative max-w-lg w-full">
            <!-- زر إغلاق -->
            <button onclick="document.getElementById('activity-modal-{{ $activity->id }}').classList.add('hidden')"
                class="absolute top-2 right-2 text-gray-600 hover:text-gray-900 font-bold text-2xl">
                &times;
            </button>
            <!-- صورة النشاط -->
            @if($activity->image)
                <img src="{{ asset('storage/activities/' . $activity->image) }}" alt="Activity Photo"
                    class="w-full h-auto rounded">
            @else
                <p class="text-center text-gray-500">No activity photo available</p>
            @endif

            <!-- وصف النشاط -->
            <div class="mt-4">
                <h3 class="text-lg font-semibold">{{ $activity->name }}</h3>
                <p class="text-gray-700 mt-2">{{ $activity->description ?? 'No description available' }}</p>
                <p class="mt-2 font-medium">Duration: {{ $activity->duration }} {{ $activity->duration_unit }}</p>
                <p class="mt-1 font-medium">Price: ${{ $activity->price }}</p>
                <p class="mt-1 font-medium">Category: {{ $activity->category }}</p>
            </div>
        </div>
    </div>
</td>