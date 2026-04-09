@push('styles')
        <link rel="stylesheet" href="{{ asset('css/transport.css') }}">
        <link rel="stylesheet" href="{{ asset('css/vehicles.css') }}">
    @endpush
<x-app-layout>
    <div class="container mx-auto px-4 sm:px-8 py-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Assignments</h1>
            <button
                id="create-assignment-btn"
                class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
            >
                Create Assignment
            </button>
        </div>

        @if (session('success'))
            <div class="mb-4 rounded-md bg-green-100 px-4 py-3 text-sm text-green-800">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 rounded-md bg-red-100 px-4 py-3 text-sm text-red-800">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

         <form method="GET" action="{{ route('assignments.index') }}" class="flex flex-wrap gap-4 items-end mb-6">
            <div class="flex-1 min-w-[220px]">
                <label class="text-sm text-gray-600">Search</label>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="car model,plate_number,driver name,shift name"
                    class="w-full border rounded-lg p-2"
                >
            </div>
                

            <div class="flex gap-2">
                <button class="bg-blue-600 text-white px-4 py-2 rounded-lg">Filter</button>
                <a href="{{ route('assignments.index') }}" class="px-4 py-2 border rounded-lg text-gray-600">Reset</a>
            </div>
        </form>
        <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white shadow-sm">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Vehicle</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Driver Name</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Shift Name</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody id="assignments-table-body" class="divide-y divide-gray-200 bg-white">
                    @forelse ($assignments as $assignment)
                        @php
                            $assignment->loadMissing('vehicle', 'driver.user', 'shiftTemplate');
                        @endphp
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $assignment->vehicle?->car_model ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                              {{ trim(($assignment->driver?->user?->name ?? '') . ' ' . ($assignment->driver?->user?->last_name ?? '')) ?: '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $assignment->shiftTemplate?->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div class="flex flex-wrap items-center gap-3">
                                    <button
                                        type="button"
                                        class="rounded-md bg-emerald-100 px-3 py-1.5 text-xs font-medium text-emerald-700 hover:bg-emerald-200 edit-assignment-btn"
                                        data-id="{{ $assignment->id }}"
                                        data-vehicle-id="{{ $assignment->transport_vehicle_id }}"
                                        data-driver-id="{{ $assignment->driver_id }}"
                                        data-shift-id="{{ $assignment->shift_template_id }}"
                                    >
                                        Edit
                                    </button>

                                    <button
                                        type="button"
                                        class="rounded-md bg-indigo-100 px-3 py-1.5 text-xs font-medium text-indigo-700 hover:bg-indigo-200 full-info-btn"
                                        data-assignment='@json($assignment)'
                                    >
                                        Full Information
                                    </button>

                                    <form action="{{ route('assignments.destroy', $assignment) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this assignment?');">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="rounded-md bg-red-100 px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-200"
                                        >
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-sm text-gray-500">No assignments found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>


   


    {{-- Create / Edit Modal --}}
    <div id="assignment-form-modal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/40"></div>
        <div class="relative mx-auto mt-20 w-full max-w-xl rounded-lg bg-white shadow-xl">
            <form id="assignment-form" method="POST" action="" class="p-6">
                @csrf
                 <div id="assignment-form-method"></div>

                <h2 id="assignment-form-title" class="mb-5 text-lg font-semibold text-gray-900"></h2>

                <div class="space-y-4">
                    <div>
                        <label for="transport_vehicle_id" class="mb-1 block text-sm font-medium text-gray-700">Vehicle</label>
                        <select id="transport_vehicle_id" name="transport_vehicle_id" class="block w-full rounded-md border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" required></select>
                    </div>

                    <div>
                        <label for="driver_id" class="mb-1 block text-sm font-medium text-gray-700">Driver</label>
                        <select id="driver_id" name="driver_id" class="block w-full rounded-md border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" required></select>
                    </div>

                    <div>
                        <label for="shift_template_id" class="mb-1 block text-sm font-medium text-gray-700">Shift</label>
                        <select id="shift_template_id" name="shift_template_id" class="block w-full rounded-md border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" required></select>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" id="assignment-form-cancel" class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Cancel</button>
                    <button type="submit" id="assignment-form-submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"></button>
                </div>
            </form>
        </div>
    </div>



{{-- Full Info Modal --}}
    <div id="assignment-info-modal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/40"></div>
        <div class="relative mx-auto mt-20 w-full max-w-2xl rounded-lg bg-white shadow-xl">
            <div class="p-6">
                <div class="mb-5 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Assignment Full Information</h2>
                    <button id="assignment-info-close" class="rounded-md border border-gray-300 px-3 py-1 text-sm text-gray-700 hover:bg-gray-50">Close</button>
                </div>

                <div id="assignment-info-content" class="grid grid-cols-1 gap-4 sm:grid-cols-2"></div>
            </div>
        </div>
    </div>

   @push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const createBtn = document.getElementById('create-assignment-btn');
    const tableBody = document.getElementById('assignments-table-body');

    const formModal = document.getElementById('assignment-form-modal');
    const form = document.getElementById('assignment-form');
    const formMethodField = document.getElementById('assignment-form-method');
    const formTitle = document.getElementById('assignment-form-title');
    const formSubmit = document.getElementById('assignment-form-submit');
    const formCancel = document.getElementById('assignment-form-cancel');

    const vehicleSelect = document.getElementById('transport_vehicle_id');
    const driverSelect = document.getElementById('driver_id');
    const shiftSelect = document.getElementById('shift_template_id');

    const infoModal = document.getElementById('assignment-info-modal');
    const infoClose = document.getElementById('assignment-info-close');
    const infoContent = document.getElementById('assignment-info-content');

    const openModal = (modal) => modal.classList.remove('hidden');
    const closeModal = (modal) => modal.classList.add('hidden');

    const createEndpoint = "{{ route('assignments.create') }}";
    const storeEndpoint = "{{ route('assignments.store') }}";
    const editBaseEndpoint = "{{ url('/admin/assignments/edit') }}";
    const updateBaseEndpoint = "{{ url('/admin/assignments/update') }}";

    async function fetchJson(url) {
        const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        if (!response.ok) throw new Error(`Request failed: ${response.status}`);
        return response.json();
    }

    function optionLabelForVehicle(vehicle) {
        const name = vehicle?.car_model ?? 'N/A';
        const plate = vehicle?.plate_number ?? 'N/A';
        return `${name} (${plate})`;
    }

    function optionLabelForDriver(driver) {
        return `${driver?.user?.name ?? 'N/A'} ${driver?.user?.last_name ?? ''}`.trim();
    }

    function optionLabelForShift(shift) {
        const days = Array.isArray(shift?.days_of_week) && shift.days_of_week.length
            ? shift.days_of_week.join(', ')
            : 'N/A';
        return `${shift?.name ?? 'N/A'} (${days})`;
    }

    function populateSelect(select, items, labelBuilder, selectedValue = null, placeholder = 'Select option') {
        select.innerHTML = `<option value="" disabled ${selectedValue ? '' : 'selected'}>${placeholder}</option>`;
        items.forEach(item => {
            const option = document.createElement('option');
            option.value = item.id;
            option.textContent = labelBuilder(item);
            if (String(selectedValue) === String(item.id)) option.selected = true;
            select.appendChild(option);
        });
    }

    function fillFormOptions(data, selected = {}) {
        populateSelect(vehicleSelect, data.vehicles || [], optionLabelForVehicle, selected.vehicle_id, 'Select Vehicle');
        populateSelect(driverSelect, data.drivers || [], optionLabelForDriver, selected.driver_id, 'Select Driver');
        populateSelect(shiftSelect, data.shiftTemplates || [], optionLabelForShift, selected.shift_id, 'Select Shift');
    }

    async function handleCreateClick() {
        try {
            const data = await fetchJson(createEndpoint);
            form.action = storeEndpoint;
            formMethodField.innerHTML = '';
            formTitle.textContent = 'Create Assignment';
            formSubmit.textContent = 'Create';
            formSubmit.className = 'rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700';
            fillFormOptions(data);
            openModal(formModal);
        } catch (error) {
            alert('Failed to load create data.');
        }
    }

    async function handleEditClick(button) {
        const assignmentId = button.dataset.id;
        try {
            const data = await fetchJson(`${editBaseEndpoint}/${assignmentId}`);
            form.action = `${updateBaseEndpoint}/${assignmentId}`;
            formMethodField.innerHTML = '<input type="hidden" name="_method" value="PUT">';
            formTitle.textContent = 'Edit Assignment';
            formSubmit.textContent = 'Update';
            formSubmit.className = 'rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700';
            fillFormOptions(data, {
                vehicle_id: button.dataset.vehicleId,
                driver_id: button.dataset.driverId,
                shift_id: button.dataset.shiftId,
            });
            openModal(formModal);
        } catch (error) {
            alert('Failed to load edit data.');
        }
    }

    function infoItem(label, value) {
        return `
            <div class="rounded-md border border-gray-200 p-3">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">${label}</p>
                <p class="mt-1 text-sm text-gray-900">${value || '-'}</p>
            </div>
        `;
    }

    function handleFullInfoClick(button) {
        const assignment = JSON.parse(button.dataset.assignment);
        const vehicle = assignment.vehicle || {};
        const categoryValue = typeof vehicle.category === 'string'
            ? vehicle.category
            : (vehicle.category?.name || '-');
        const driver = assignment.driver || {};
        const user = driver.user || {};
        const shift = assignment.shift_template || {};
        const driverFullName = `${user.name || ''} ${user.last_name || ''}`.trim() || '-';
        const shiftDays = Array.isArray(shift.days_of_week) && shift.days_of_week.length
            ? shift.days_of_week.join(', ')
            : '-';
        infoContent.innerHTML = [
            infoItem('Vehicle Name', vehicle.car_model || '-'),
            infoItem('Plate Number', vehicle.plate_number || '-'),
            infoItem('Category', categoryValue),
            infoItem('Type', vehicle.type || '-'),
            infoItem('Driver Full Name', driverFullName),
            infoItem('Shift Name', shift.name || '-'),
            infoItem('Shift Days', shiftDays),
        ].join('');
        openModal(infoModal);
    }

    createBtn.addEventListener('click', handleCreateClick);
    formCancel.addEventListener('click', () => { form.reset(); closeModal(formModal); });
    infoClose.addEventListener('click', () => closeModal(infoModal));

    tableBody.addEventListener('click', event => {
        const editBtn = event.target.closest('.edit-assignment-btn');
        if (editBtn) { handleEditClick(editBtn); return; }
        const fullInfoBtn = event.target.closest('.full-info-btn');
        if (fullInfoBtn) handleFullInfoClick(fullInfoBtn);
    });
});
</script>
@endpush
</x-app-layout>

