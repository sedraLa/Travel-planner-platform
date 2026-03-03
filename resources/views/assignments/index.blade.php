@php use App\Enums\UserRole; @endphp
<x-app-layout>
<div class="container mx-auto px-4 sm:px-8">
    <div class="py-8">
        {{-- رأس الصفحة وزر الإضافة --}}
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-semibold leading-tight">Assignments</h2>
            <button id="create-btn" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Create New Assignment
            </button>
        </div>

        {{-- جدول عرض التعيينات --}}
        <div class="-mx-4 sm:-mx-8 px-4 sm:px-8 py-4 overflow-x-auto">
            <div class="inline-block min-w-full shadow rounded-lg overflow-hidden">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Vehicle</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Driver</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Shift</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="assignments-table-body">
                        @forelse ($assignments as $assignment)
                        {{-- التأكد من تحميل كل العلاقات المطلوبة للعرض الفوري --}}
                        @php $assignment->loadMissing('vehicle.category', 'driver.user', 'shiftTemplate'); @endphp
                        <tr>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <p class="text-gray-900 whitespace-no-wrap">{{ $assignment->vehicle?->car_model ?? '-' }}</p>
                                <p class="text-gray-600 whitespace-no-wrap text-xs">{{ $assignment->vehicle?->plate_number ?? '' }}</p>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <p class="text-gray-900 whitespace-no-wrap">{{ $assignment->driver?->user?->name ?? '-' }} {{ $assignment->driver?->user?->last_name ?? '' }}</p>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <p class="text-gray-900 whitespace-no-wrap">{{ $assignment->shiftTemplate?->name ?? '-' }}</p>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm flex items-center gap-3">
                                <button class="font-medium text-blue-600 hover:text-blue-800 view-btn"
                                        data-details="{{ htmlspecialchars(json_encode($assignment), ENT_QUOTES, 'UTF-8') }}">
                                    View
                                </button>
                                <button class="font-medium text-green-600 hover:text-green-800 edit-btn"
                                        data-id="{{ $assignment->id }}"
                                        data-vehicle-id="{{ $assignment->transport_vehicle_id }}"
                                        data-driver-id="{{ $assignment->driver_id }}"
                                        data-shift-id="{{ $assignment->shift_template_id }}">
                                    Edit
                                </button>
                                <form action="{{ route('assignments.destroy', $assignment) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this assignment?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="font-medium text-red-600 hover:text-red-800">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-10 text-gray-500">No assignments found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- ================================================================= --}}
{{-- ===================  النافذة المنبثقة الموحدة  =================== --}}
{{-- ================================================================= --}}
<div id="assignment-modal" class="fixed z-50 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="assignment-form" method="POST" action="">
                @csrf
                <div id="method-field"></div> {{-- لحقن @method('PUT') --}}
                
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title"></h3>

                    {{-- قسم عرض المعلومات (يظهر فقط في وضع العرض) --}}
                    <div id="view-info" class="mt-4 hidden"></div>

                    {{-- قسم حقول الإدخال (يظهر في وضع الإضافة والتعديل) --}}
                    <div id="form-fields" class="mt-4 space-y-4 hidden">
                        <div>
                            <label for="vehicle_id" class="block text-sm font-medium text-gray-700">Vehicle</label>
                            <select id="vehicle_id" name="transport_vehicle_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md" required></select>
                        </div>
                        <div>
                            <label for="driver_id" class="block text-sm font-medium text-gray-700">Driver</label>
                            <select id="driver_id" name="driver_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md" required></select>
                        </div>
                        <div>
                            <label for="shift_template_id" class="block text-sm font-medium text-gray-700">Shift</label>
                            <select id="shift_template_id" name="shift_template_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md" required></select>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button id="submit-btn" type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white sm:ml-3 sm:w-auto sm:text-sm"></button>
                    <button id="cancel-btn" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // --- عناصر الواجهة ---
    const modal = document.getElementById('assignment-modal');
    const form = document.getElementById('assignment-form');
    const title = document.getElementById('modal-title');
    const submitBtn = document.getElementById('submit-btn');
    const cancelBtn = document.getElementById('cancel-btn');
    const viewInfoSection = document.getElementById('view-info');
    const formFieldsSection = document.getElementById('form-fields');
    const methodField = document.getElementById('method-field');
    const vehicleSelect = document.getElementById('vehicle_id');
    const driverSelect = document.getElementById('driver_id');
    const shiftSelect = document.getElementById('shift_template_id');
    const tableBody = document.getElementById('assignments-table-body');

    // --- دوال مساعدة ---
    const openModal = () => modal.classList.remove('hidden');
    const closeModal = () => {
        modal.classList.add('hidden');
        form.reset();
        methodField.innerHTML = '';
    };
    cancelBtn.addEventListener('click', closeModal);

    function populateSelects(data, selected = {}) {
        vehicleSelect.innerHTML = '<option value="" disabled>Select Vehicle</option>';
        data.vehicles.forEach(v => {
            const isSelected = selected.vehicle_id == v.id ? 'selected' : '';
            vehicleSelect.innerHTML += `<option value="${v.id}" ${isSelected}>${v.car_model} (${v.plate_number})</option>`;
        });

        driverSelect.innerHTML = '<option value="" disabled>Select Driver</option>';
        data.drivers.forEach(d => {
            const driverName = `${d.user?.name || 'N/A'} ${d.user?.last_name || ''}`.trim();
            const isSelected = selected.driver_id == d.id ? 'selected' : '';
            driverSelect.innerHTML += `<option value="${d.id}" ${isSelected}>${driverName}</option>`;
        });

        shiftSelect.innerHTML = '<option value="" disabled>Select Shift</option>';
        data.shiftTemplates.forEach(s => {
            const days = Array.isArray(s.days_of_week) ? s.days_of_week.join(', ') : 'N/A';
            const isSelected = selected.shift_id == s.id ? 'selected' : '';
            shiftSelect.innerHTML += `<option value="${s.id}" ${isSelected}>${s.name} (${days})</option>`;
        });
        
        if (!selected.vehicle_id) vehicleSelect.selectedIndex = 0;
        if (!selected.driver_id) driverSelect.selectedIndex = 0;
        if (!selected.shift_id) shiftSelect.selectedIndex = 0;
    }

    // --- معالج زر الإضافة (Create) ---
    document.getElementById('create-btn').addEventListener('click', () => {
        form.action = "{{ route('assignments.store') }}";
        methodField.innerHTML = '';
        title.textContent = 'Create New Assignment';
        submitBtn.textContent = 'Save';
        submitBtn.className = 'w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 sm:ml-3 sm:w-auto sm:text-sm';
        
        viewInfoSection.classList.add('hidden');
        formFieldsSection.classList.remove('hidden');
        submitBtn.classList.remove('hidden');

        fetch("{{ route('assignments.create') }}")
            .then(response => response.json())
            .then(data => {
                populateSelects(data);
                openModal();
            });
    });

    // --- استخدام Event Delegation لمعالجة الأزرار داخل الجدول ---
    if (tableBody) {
        tableBody.addEventListener('click', function(e) {
            const target = e.target;

            // --- معالج زر العرض (View) ---
            const viewBtn = target.closest('.view-btn');
            if (viewBtn) {
                e.preventDefault();
                const data = JSON.parse(viewBtn.dataset.details);

                title.textContent = 'Assignment Details';
                formFieldsSection.classList.add('hidden');
                viewInfoSection.classList.remove('hidden');
                submitBtn.classList.add('hidden');

                const driverName = `${data.driver?.user?.name || ''} ${data.driver?.user?.last_name || ''}`.trim() || '-';
                const shiftDays = data.shift_template?.days_of_week?.join(', ') || 'N/A';

                viewInfoSection.innerHTML = `
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">
                        <div class="sm:col-span-1"><dt class="text-sm font-medium text-gray-500">Vehicle</dt><dd class="mt-1 text-sm text-gray-900">${data.vehicle?.car_model || '-'}</dd></div>
                        <div class="sm:col-span-1"><dt class="text-sm font-medium text-gray-500">Plate Number</dt><dd class="mt-1 text-sm text-gray-900">${data.vehicle?.plate_number || '-'}</dd></div>
                        <div class="sm:col-span-1"><dt class="text-sm font-medium text-gray-500">Category</dt><dd class="mt-1 text-sm text-gray-900">${data.vehicle?.category?.name || '-'}</dd></div>
                        <div class="sm:col-span-1"><dt class="text-sm font-medium text-gray-500">Type</dt><dd class="mt-1 text-sm text-gray-900">${data.vehicle?.type || '-'}</dd></div>
                        <div class="sm:col-span-1"><dt class="text-sm font-medium text-gray-500">Driver</dt><dd class="mt-1 text-sm text-gray-900">${driverName}</dd></div>
                        <div class="sm:col-span-1"><dt class="text-sm font-medium text-gray-500">Shift</dt><dd class="mt-1 text-sm text-gray-900">${data.shift_template?.name || '-'}</dd></div>
                        <div class="sm:col-span-2"><dt class="text-sm font-medium text-gray-500">Shift Days</dt><dd class="mt-1 text-sm text-gray-900">${shiftDays}</dd></div>
                    </dl>`;
                
                openModal();
                return;
            }

            // --- معالج زر التعديل (Edit) ---
            const editBtn = target.closest('.edit-btn');
            if (editBtn) {
                e.preventDefault();
                const id = editBtn.dataset.id;
                
                form.action = `{{ url('/admin/assignments/update') }}/${id}`;
                methodField.innerHTML = `<input type="hidden" name="_method" value="PUT">`;
                title.textContent = 'Edit Assignment';
                submitBtn.textContent = 'Update';
                submitBtn.className = 'w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 sm:ml-3 sm:w-auto sm:text-sm';
                
                viewInfoSection.classList.add('hidden');
                formFieldsSection.classList.remove('hidden');
                submitBtn.classList.remove('hidden');

                fetch(`{{ url('/admin/assignments/edit') }}/${id}`)
                    .then(res => res.json())
                    .then(data => {
                        const selected = {
                            vehicle_id: editBtn.dataset.vehicleId,
                            driver_id: editBtn.dataset.driverId,
                            shift_id: editBtn.dataset.shiftId
                        };
                        populateSelects(data, selected);
                        openModal();
                    });
                return;
            }
        });
    }
});
</script>
@endpush
</x-app-layout>
