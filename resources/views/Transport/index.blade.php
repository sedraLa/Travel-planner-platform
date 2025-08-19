<x-app-layout>
    @push('styles')
    <link rel="stylesheet" href="{{asset('css/transport.css')}}">
     @endpush
    <div class="heading">
        <div class="header" style="width: 100%; justify-content: space-between; align-items: center;">
            <div style="display: flex; gap: 5px; align-items: center;">
                <img id="head-icon" src="{{ asset('images/icons/car-side-solid-full.svg') }}" alt="icon">
                <h2>Transport Services</h2>
            </div>
            <!-- popup -->
            <button id="popup-btn" class="add-btn">+ Add New Service</button>
        </div>
        
        <p>We offer the best transport services with the best prices</p>
        @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-green-100 text-green-800 rounded">
            {{session('success')}}
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

                <!--service name-->
                <h3>{{ $transport->name }}</h3>
                <!--service description-->
                <p>{{ $transport->description }}</p>

                <div class="information">
                    <div class="top">
                        <div class="content">
                            <img class="icon" src="{{asset('images/icons/user-group-solid-full.svg')}}">
                            <span class="card-span">Choose your ride</span>
                        </div>
                        <div class="content">
                            <img class="icon" src="{{asset('images/icons/clock-regular-full.svg')}}">
                            <span class="card-span">Available 24/7</span>
                        </div>
                    </div>
                    <!--price-->
                    <div class="bottom">
                        <p class="options">See vehicle options</p>
                        <button class="order-btn">order car</button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="bottom-section">
            <div class="header">
                <h2>What to expect</h2>
            </div>
            <div class="expects">
                <div class="first">
                    <img src="{{asset('images/icons/car-side-solid-full.svg')}}" alt="icon" class="bottom-icon">
                    <h4>Premium vehicles</h4>
                    <p>well-maintained comfortable vehicles</p>
                </div>

                <div class="second">
                    <img src="{{asset('images/icons/user-tie-solid-full.svg')}}" alt="icon" class="bottom-icon">
                    <h4>Proffesional drivers</h4>
                    <p>Licensed and experienced drivers</p>
                </div>

                <div class="third">
                    <img src="{{asset('images/icons/clock-regular-full.svg')}}" alt="icon" class="bottom-icon">
                    <h4>On-time services</h4>
                    <p>Punctual and reliable transport</p>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Popup Overlay -->
    <div id="popup-overlay" style="display: {{ $errors->any() ? 'flex' : 'none' }};">
        <div class="popup">
            <h2>Add New Service</h2>
            <form action="{{ route('transport.store') }}" method="post" enctype="multipart/form-data">
                @csrf

                <!--Error messages-->
                @if ($errors->any())
                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded text-sm">
                    @foreach ($errors->all() as $error)
                        <div class="mb-1">• {{ $error }}</div>
                    @endforeach
                </div>
            @endif
            
                <div class="first-section">
                    <div class="right">
                        {{-- Name --}}
                        <x-input-label for="name" value="Service Name"/>
                        <x-text-input id="name" type="text" name="name" required autofocus placeholder="Enter service name"/>
                    </div>

                    <div class="left">
                        <x-input-label for="type" value="Service Type"/>
                        <select name="type" id="type" required>
                            <option value="Airport pick up">Airport pick up</option>
                            <option value="city tour">city tour</option>
                            <option value="private transport">private transport</option>
                        </select>
                    </div>
                </div>

                {{-- Description --}}
                <x-input-label for="description" value="Description" />
                <textarea id="description" name="description" required placeholder="Enter description"></textarea>

                {{-- Image --}}
                <x-input-label for="image" value="Service Image" />
                <input type="file" id="image" name="image" required>

                <div class="first-section">
                    <div class="right">
                        {{-- Price --}}
                        <x-input-label for="price" value="Price" />
                        <x-text-input id="price" name="price" required placeholder="$ Price per trip"/>
                    </div>

                    <div class="left">
                        <x-input-label for="passengers" value="Max Passengers" />
                        <input type="number" id="passengers" name="max_passengers" min="1" placeholder="Enter max passengers" required>
                    </div>
                </div>

                {{-- Buttons --}}
                <div class="popup-buttons">
                    <a href="{{route('transport.store')}}">
                    <button type="submit" class="btn btn-primary">Save</button>
                    </a>
                    <button type="button" id="close-popup" style='background: #ccc;'>Cancel</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

<!-- Script -->
<script>
    const popupOverlay = document.getElementById('popup-overlay');
    const addServiceBtn = document.getElementById('popup-btn'); 
    const closePopupBtn = document.getElementById('close-popup');

    addServiceBtn.addEventListener('click', () => {
        popupOverlay.style.display = 'flex';
    });

    closePopupBtn.addEventListener('click', () => {
        popupOverlay.style.display = 'none';
    });

    window.addEventListener('click', (e) => {
        if (e.target === popupOverlay) {
            popupOverlay.style.display = 'none';
        }
    });
</script>
