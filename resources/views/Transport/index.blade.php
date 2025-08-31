@php use App\Enums\UserRole; @endphp
<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/transport.css') }}">
    @endpush
    @push('styles')
    <link rel="stylesheet" href="{{asset('css/destinations.css')}}">
    @endpush
    <div class="main-wrapper">
        @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-green-200 text-green-800 rounded">
            {{ session('success') }}
        </div>
    @endif
        <div class="hero-background transport-page">
            <div class="heading">
                <div class="header">
                    <div style="display: flex; gap: 5px; align-items: center;">
                        <h2 style="font-size:80px;font-weight:500">Transport Services</h2>
                    </div>
                    
                </div>
        
                <p style="color:white;font-size:20px;width:50%">choose from our curated selection of premium tranportation options each designed to evelate your travel experience</p>
                <div class="flex justify-end mb-4 px-6 pt-6">
                @if (Auth::user()->role === UserRole::ADMIN->value)
                        <button class="add-btn" id="popup-btn">+ Add New Service</button>
                    @endif
                </div>
               
            </div>
        </div>


    <div class="main">
        <div class="transport-container">
            <div class="cards" style="margin: 100px auto;">
                @foreach ($transports as $transport)
                    <div class="card" >
                        <div class="service-image">
                            <img src="{{ asset('storage/' . $transport->image) }}" alt="transport service">
                        </div>
                        <h3>{{ $transport->name }}</h3>
                        <p>{{ $transport->description }}</p>

                        <div class="information">
                            <div class="top">
                                <div class="content">
                                    <img class="icon" src="{{ asset('images/icons/user-group-solid-full.svg') }}">
                                    <span class="card-span">{{ $transport->type }}</span>
                                </div>
                                <div class="content">
                                    <img class="icon" src="{{ asset('images/icons/clock-regular-full.svg') }}">
                                    <span class="card-span">Available 24/7</span>
                                </div>
                            </div>
                            <div class="bottom">
                                @if(Auth::user()->role === UserRole::ADMIN->value)
                                <p class="options">Manage this Transport</p>
                                @else
                                <p class="options">See vehicle options</p>
                                @endif


                                @if(Auth::user()->role === UserRole::ADMIN->value)
                                    <div class="manage-btn">
                                        <button class="order-btn edit-btn"
                                            data-id="{{ $transport->id }}"
                                            data-edit-id="{{ $transport->id }}"
                                            data-name="{{ $transport->name }}"
                                            data-type="{{ $transport->type }}"
                                            data-description="{{ $transport->description }}">
                                            Edit
                                        </button>
                                        
                                        <form action="{{route('transport.destroy',$transport->id)}}" method="post"
                                            onsubmit="return confirm('Are you sure you want to delete this transport?');">
                                            @csrf
                                            @method('DELETE')
                                        <button type="submit" class="order-btn" style="background-color:#de2222;">Delete</button>
                                        </form>
                                      
                                    </div>
                                
                                @else
                                    <button class="order-btn">order car</button>
                                @endif
                            </div>
                            <!--Add vehicles button-->
                            <a href="{{ route('vehicle.create', ['transport_id' => $transport->id]) }}">
                                <button class="add-vehicle-btn" style="border:2px solid #3d3d92">Add Vehicles +</button>
                            </a>

                            <a href="{{ route('transport.show', $transport->id) }}">
                                <button class="add-vehicle-btn" style="border:2px solid #3d3d92;margin-left:5px">View Vehicles</button>
                            </a>
                            
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

</div>
    <!-- Add/Edit Popup Overlay -->
    <div id="popup-overlay" style="display: {{ $errors->any() ? 'flex' : 'none' }};">
        <div class="popup">
            <h2 id="popup-title">Add New Service</h2>
            <form id="popup-form" action="{{ route('transport.store') }}" method="post" enctype="multipart/form-data"
            data-add-action="{{ route('transport.store') }}">
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

                <!--To change form method when edit-->
                <input type="hidden" name="_method" id="method-spoof" value="PUT" disabled>
                <input type="hidden" name="edit_id" id="edit-id" value="">

                <x-input-label for="popup-name" value="Service Name"/>
                <x-text-input id="popup-name" type="text" name="name" required placeholder="Enter service name"/>

                <x-input-label for="popup-type" value="Service Type"/>
                <select name="type" id="popup-type" required>
                    <option value="Airport pick up">Airport pick up</option>
                    <option value="City tour">City tour</option>
                    <option value="Private transport">Private transport</option>
                </select>

                <x-input-label for="popup-description" value="Description"/>
                <textarea id="popup-description" name="description" required placeholder="Enter description"></textarea>

                <x-input-label for="popup-image" value="Service Image"/>
                <input type="file" id="popup-image" name="image">

                <div class="popup-buttons">
                    <button type="submit" class="btn btn-primary" id="popup-save-btn">Save</button>
                    <button type="button" id="close-popup" style="background:#ccc;">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    

</x-app-layout>


<script>
document.addEventListener('DOMContentLoaded', () => {
    const popupOverlay = document.getElementById('popup-overlay');
    const popupForm = document.getElementById('popup-form');
    const popupTitle = document.getElementById('popup-title');
    const closePopupBtn = document.getElementById('close-popup');

    const nameInput = document.getElementById('popup-name');
    const typeSelect = document.getElementById('popup-type');
    const descTextarea = document.getElementById('popup-description');
    const methodInput = document.getElementById('method-spoof');
    const editIdInput = document.getElementById('edit-id');

    // Add Service
    const addServiceBtn = document.getElementById('popup-btn');
    addServiceBtn?.addEventListener('click', () => {
        popupOverlay.style.display = 'flex';
        popupTitle.textContent = 'Add New Service';
        popupForm.action = popupForm.dataset.addAction;

        methodInput.disabled = true;
        methodInput.value = '';
        editIdInput.value = '';

        nameInput.value = '';
        typeSelect.value = 'Airport pick up';
        descTextarea.value = '';
    });

    // Edit Service
    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.id;

            popupOverlay.style.display = 'flex';
            popupTitle.textContent = 'Edit Service';
            popupForm.action = `/transports/${id}`;

            //change form method to PUT
            methodInput.disabled = false;
            methodInput.value = 'PUT';
            editIdInput.value = id;

            nameInput.value = btn.dataset.name;
            descTextarea.value = btn.dataset.description;

            const typeValue = btn.dataset.type.trim().toLowerCase();
            Array.from(typeSelect.options).forEach(option => {
                option.selected = (option.value.trim().toLowerCase() === typeValue);
            });
        });
    });

    // Close Popup
    closePopupBtn?.addEventListener('click', () => popupOverlay.style.display = 'none');
    window.addEventListener('click', e => {
        if (e.target === popupOverlay) popupOverlay.style.display = 'none';
    });

    // Reload popup after validation error
    @if($errors->any())
        popupOverlay.style.display = 'flex';

        @if(old('edit_id'))
            popupTitle.textContent = 'Edit Service';
            popupForm.action = `/transports/{{ old('edit_id') }}`;
            methodInput.disabled = false;
            methodInput.value = 'PUT';
            editIdInput.value = "{{ old('edit_id') }}";
        @else
            popupTitle.textContent = 'Add New Service';
            popupForm.action = popupForm.dataset.addAction;
            methodInput.disabled = true;
            methodInput.value = '';
            editIdInput.value = '';
        @endif

        nameInput.value = "{{ old('name') }}";
        descTextarea.value = "{{ old('description') }}";
        const typeValue = "{{ old('type', 'Airport pick up') }}".trim().toLowerCase();
        Array.from(typeSelect.options).forEach(option => {
            option.selected = (option.value.trim().toLowerCase() === typeValue);
        });
    @endif
});
</script>

