@php use App\Enums\UserRole; @endphp
<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/transport.css') }}">
    @endpush

    <div class="heading">
        <div class="header" style="width: 100%; justify-content: space-between; align-items: center;">
            <div style="display: flex; gap: 5px; align-items: center;">
                <img id="head-icon" src="{{ asset('images/icons/car-side-solid-full.svg') }}" alt="icon">
                <h2>Transport Services</h2>
            </div>
            @if (Auth::user()->role === UserRole::ADMIN->value)
                <button class="add-btn" id="popup-btn">+ Add New Service</button>
            @endif
        </div>

        <p>We offer the best transport services with the best prices</p>

        @if(session('success'))
            <div class="mb-4 px-4 py-3 bg-green-100 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif
    </div>

    <div class="main">
        <div class="transport-container">
            <div class="cards">
                @foreach ($transports as $transport)
                    <div class="card">
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
                                <p class="options">See vehicle options</p>

                                @if(Auth::user()->role === UserRole::ADMIN->value)
                                    <div class="manage-btn">
                                        <button class="order-btn edit-btn"
                                            data-id="{{ $transport->id }}"
                                            data-name="{{ $transport->name }}"
                                            data-type="{{ $transport->type }}"
                                            data-description="{{ $transport->description }}">
                                            Edit
                                        </button>
                                        <button class="order-btn" style="background-color:red;">Delete</button>
                                    </div>
                                @else
                                    <button class="order-btn">order car</button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Add/Edit Popup Overlay -->
    <div id="popup-overlay" style="display: none;">
        <div class="popup">
            <h2 id="popup-title">Add New Service</h2>
            <form id="popup-form" action="{{ route('transport.store') }}" method="post" enctype="multipart/form-data"
            data-add-action="{{ route('transport.store') }}">
                @csrf
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

    <script src="{{ asset('js/transport-popup.js') }}"></script>

</x-app-layout>
