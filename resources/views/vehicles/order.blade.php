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
            <div class="form-header">
                <h2>Where would you like to go ?</h2>
                <p>Fill in your trip details to find the perfect vehicle</p>
            </div>
            <div class="form-container">
              <form action="{{ route('vehicle.search', $transport->id) }}" method="POST">
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

                <div class="second-section">
                    <div class="container">
                        <div class="head-row">
                            <img class="icon" src="{{asset('images/icons/calendar-days-solid-full (1).svg')}}" alt="icon">
                            <label for="pickup_datetime">pickup Date & Time</label>
                        </div>
                        <x-text-input type="datetime-local" name="pickup_datetime" id="pickup_datetime" min="{{now()->format('Y-m-d\TH:i')}}"/>
                      </div>

                      <div class="container">
                        <div class="head-row">
                            <img class="icon" src="{{asset('images/icons/user-group-solid-full (1).svg')}}" alt="icon">
                            <label for="passengers">Number of passengers</label>
                        </div>
                        <x-text-input type="number" name="passengers" id="passengers" placeholder="1 Passenger" min="1"/>
                      </div>

                </div>
                <button class="order-car" type="submit">Find Available Cars</button>


                </form>
            </div>

          </div>

</x-app-layout>
