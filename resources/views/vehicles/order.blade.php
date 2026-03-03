<x-app-layout>
  @push('styles')
  <link rel="stylesheet" href="{{asset("css/ordercar.css")}}">
  @endpush
<div class="main-wrapper">
        <div class="hero-background">
          <div class="headings">
            <h1>Book Your <span>Perfect Ride</span></h1>
            <p>Premium transportation at your fingerprints</p>
          </div>
        </div>

        <div class="main-container">
            @if ($errors->any())
            <div class="mb-4 px-4 py-3 bg-red-100 text-red-800 rounded">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if($message)
        <div class="mb-4 px-4 py-3 bg-red-100 text-red-800 rounded">
        {{ $message }}
    </div>
@endif
            <div class="form-header">
                @if (session('success'))
                <div class="mb-4 px-4 py-3 bg-green-100 text-green-800 rounded">
                    {{ session('success') }}
                </div>
            @endif
        

                <h2>Where would you like to go ?</h2>
                <p>Fill in your trip details to find the perfect vehicle</p>
            </div>
            <div class="form-container">
              <form action="{{ route('vehicle.search') }}" method="POST">
                @csrf
                <div class="first-section">
                  <div class="container">
                    <div class="head-row">
                        <img class="icon" src="{{asset('images/icons/location-dot-solid-full (2).svg')}}" alt="icon">
                        <label for="pickup_location">Pick-up Location</label>
                    </div>
                  <x-text-input type="text" name="pickup_location" id="pickup_location" placeholder="Enter pickup address"/>
                  </div>

                  <div class="container">
                    <div class="head-row">
                        <img class="icon" src="{{asset('images/icons/location-dot-solid-full (2).svg')}}" alt="icon">
                        <label for="dropoff_location">Drop-off Location</label>
                    </div>
                    <x-text-input type="text" name="dropoff_location" id="dropoff_location" placeholder="Enter destination address"/>
                  </div>
                </div>

                <div class="second-section flex flex-wrap gap-4">
                    <div class="container flex-1 min-w-[200px]">
                        <div class="head-row">
                            <img class="icon" src="{{asset('images/icons/calendar-days-solid-full (1).svg')}}" alt="icon">
                            <label for="pickup_datetime">pickup Date & Time</label>
                        </div>
                        <x-text-input type="datetime-local" name="pickup_datetime" id="pickup_datetime" min="{{now()->format('Y-m-d\TH:i')}}"/>
                      </div>

                      <div class="container flex-1 min-w-[200px] ">
                        <div class="head-row">
                            <img class="icon" src="{{asset('images/icons/user-group-solid-full (1).svg')}}" alt="icon">
                            <label for="passengers">Number of passengers</label>
                        </div>
                        <x-text-input type="number" name="passengers" id="passengers" placeholder="1 Passenger" min="1"/>
                      </div>


              <div class="first-section flex flex-wrap gap-4">
                <!-- Vehicle Category-->
                  <div class="container flex-1 min-w-[200px]">
                    <div class="head-row">
                         <img class="icon" src="{{asset('images/icons/car-side-solid-full.svg')}}" alt="icon">
                         <label for="category">Vehicle Category</label>
                    </div>
                  <select name="category" id="category" class="  w-80 border rounded px-3 py-2 border-gray-300">
                              <option value="">Any category</option>
                              <option value="comfort" @selected(old('category') === 'comfort')>COMFORT</option>
                              <option value="economy" @selected(old('category') === 'economy')>ECONOMY</option>
                              <option value="vip" @selected(old('category') === 'vip')>VIP</option>

                         </select>
                  </div>

                    <!-- Vehicle Type-->
                  <div class="container flex-1 min-w-[200px]  ">
                    <div class="head-row">
                        <img class="icon" src="{{asset('images/icons/type.png')}}" alt="icon">
                        <label for="dropoff_location">Vehicle Type</label>
                    </div>
                    <select name="type" id="type" class="w-80 border rounded px-3 py-2   border-gray-300">
                              <option value="">Any type</option>
                              <option value="suv" @selected(old('type') === 'suv')>SUV</option>
                              <option value="van" @selected(old('type') === 'van')>VAN</option>
                              <option value="sedan" @selected(old('type') === 'sedan')>SEDAN</option>


                         </select>
                  </div>
                </div>


              </div>
                <button class="order-car" type="submit">Find Available Cars</button>


            </form>
          </div>

         </div>

</x-app-layout>
