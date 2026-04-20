@php use App\Enums\UserRole; @endphp
<x-app-layout>
@push('styles')
<link rel="stylesheet" href="{{ asset('css/cardetails.css') }}">
@endpush

<div class="main-container">

    @if (session('success'))
    <div class="mb-4 px-4 py-3 bg-green-100 text-green-800 rounded">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded text-sm">
            @foreach ($errors->all() as $error)
                <div class="mb-1">• {{ $error }}</div>
            @endforeach
        </div>
    @endif
    <header>
        
        <div class="head w-full">
        
            <div class="flex justify-between items-center">
                 <h1>All Vehicles</h1>

               <a href="{{ route('admin.vehicles.create') }}">
                   <button class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded shadow transition duration-200">
                     +Add Vehicles
                   </button>
               </a>
        </div>

            <p>Manage Vehicles For This Transport Service</p>
        </div>
    </header>



    {{-- Search Form  --}}
    <div class="bg-blue-50 p-6 rounded-2xl shadow-md mb-10">
          <form method="GET" action="{{ route('admin.vehicles.index') }}" class="flex flex-wrap gap-4 items-end mb-6">
            {{-- Keyword --}}
            <div class="flex-1 min-w-[200px]">
                <label class="text-sm text-gray-600 font-bold">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Car Model, Driver Name,plate_number" class="w-full border rounded-lg p-2">
            </div>
        
            
            {{-- Status --}}
            <div>
                <label class="text-sm text-gray-600 p-2 w-44">Category</label>
                <select name="category" class="border rounded-lg p-2" style="margin-bottom:0">
                    <option value="">All</option>
                    <option value="luxury" @selected(request('category')=='luxury')>LUXURY</option>
                    <option value="standard" @selected(request('category')=='standard')>STANDARD</option>
                    <option value="premium" @selected(request('category')=='premium')>PREMIUM</option>
                </select>
            </div>
        
            {{-- Type --}}
            <div>
                <label class="text-sm text-gray-600 p-2 w-44">Type</label>
                <select name="type" class="border rounded-lg p-2" style="margin-bottom:0">
                    <option value="">All</option>
                    <option value="sedan" @selected(request('type')=='sedan')>SEDAN</option>
                    <option value="van" @selected(request('type')=='van')>VAN</option>
                    <option value="suv" @selected(request('type')=='suv')>SUV</option>
                </select>
            </div>





             {{-- base_price--}}
            <div>
                <label class="text-sm text-gray-600">Price</label>
                <input type="number" name="base_price"  step=0.1 value="{{ request('base_price') }}" placeholder="Price"    min="0" class="border rounded-lg p-2 w-20">
            </div>


              {{--max_passengers--}}
            <div>
                <label class="text-sm text-gray-600">number of passengers</label>
                <input type="number" name="max_passengers"  step=0.1 value="{{ request('max_passengers') }}"     min="0" class="border rounded-lg p-2 w-20">
            </div>
        
            {{-- Actions --}}
            <div class="flex gap-2">
                <button class="bg-blue-600 text-white px-4 py-2 rounded-lg">Filter</button>
                <a href="{{ route('admin.vehicles.index') }}" class="px-4 py-2 border rounded-lg text-gray-600">Reset</a>
            </div>
        </form>
</div>

    <div class="cards">
        @forelse($vehicles as $vehicle)
            <div class="card">
                <div class="car-image">
                    <img src="{{ asset('storage/' . $vehicle->image) }}" alt="car image" class="car-img">
                </div>
                <div class="details">
                    <div class="top-section">
                        <div class="main-info">
                            <h2>{{ $vehicle->car_model }}/{{ $vehicle->type }}</h2>
                            <div class="align">
                                <img src="{{ asset('images/icons/user-group-solid-full.svg') }}" class="icon">
                                <p>Up to {{ $vehicle->max_passengers }} passengers / {{ $vehicle->plate_number }}</p>
                            </div>
                        </div>
                        <div class="category">
                            <span>{{ $vehicle->category }}</span>
                        </div>
                    </div>
    
                    <div class="driver-section">
                        <h4>Driver Name : <span class="driver-name">{{ $vehicle->driver?->user?->full_name ?? 'No driver assigned' }}
                        <div class="align">
                            <img src="{{ asset('images/icons/phone-solid-full.svg') }}" class="icon">
                            {{--<p>{{$vehicle->driver ? $vehicle->driver->user->phone_number : 'No driver assigned'}}</p>--}}
                            <p>{{ $vehicle->driver?->user?->phone_number ?? 'No driver assigned' }}</p>
                        </div>
                    </div>
    
                    <div class="price-section">
                        <div class="left">
                            <h4>Base Price : {{ $vehicle->base_price }}$</h4>
                            <p>+ ${{ $vehicle->price_per_km }}/km</p>
                        </div>
                        <div class="right"></div>
                    </div>
    
                    <div class="manage-btn">
                        <a href="{{ route('admin.vehicles.edit', $vehicle->id) }}">
                            <button class="order-btn edit-btn">Edit Vehicle</button>
                        </a>
    
                        <form action="{{ route('admin.vehicles.destroy', $vehicle->id) }}" method="POST" style="display:inline-block"
                              onsubmit="return confirm('Are you sure you want to delete this vehicle?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="order-btn delete-btn">Delete Vehicle</button>
                        </form>
                    </div>
    
                </div>
            </div>
        @empty
            <p style="color:black; font-size:18px; text-align:center; margin-top:50px;">
                🚗 No vehicles available for this transport service yet.
            </p>
        @endforelse
    </div>

      {{-- Pagination --}}
        <div class="pagination-wrapper">
            {{ $vehicles->appends(request()->query())->links() }}
        </div>
    
</div>


</x-app-layout>
