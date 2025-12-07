<x-app-layout>
    @push('styles')
    <link rel="stylesheet" href="{{asset("css/ordercar.css")}}">
    @endpush
  <div class="main-wrapper">

          <div class="hero-background">
            <div class="headings">
              <h1>Complete your reservation</h1>
              <p>Just a few more details and you are all set</p>

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
                  <h2>Contact Information</h2>
                  <p>We'll use this information to confirm your booking</p>
                  <p><strong>Total Price: ${{ number_format($total_price, 2) }}</strong></p>
              </div>
              <div class="form-container">
                <form id="reservation-form" action="{{ route('vehicleReservation.store', ['transportId' => $vehicle->transport_id, 'vehicleId' => $vehicle->id, 'driver_id' => $vehicle->driver_id]) }}" method="POST">
                    @csrf
                    <input type="hidden" name="pickup_location" value="{{ $pickup_location }}">
                    <input type="hidden" name="dropoff_location" value="{{ $dropoff_location }}">
                    <input type="hidden" name="pickup_datetime" value="{{ $pickup_datetime }}">
                    <input type="hidden" name="passengers" value="{{ $passengers }}">
                    <input type="hidden" name="distance" value="{{ $distance }}">
                    <input type="hidden" name="duration" value="{{ $duration }}">
                    <input type="hidden" name="total_price" value="{{ $total_price }}">
                  <div class="first-section">
                    <!--First name-->
                    <div class="container">
                      <div class="head-row">
                          <img class="icon" src="{{asset('images/icons/user-group-solid-full (1).svg')}}" alt="icon">
                          <label for="First Name">First Name</label>
                      </div>
                    <x-text-input type="text" name="name" id="First Name" placeholder="Enter First Name" value="{{ Auth::user()->name ?? '' }}"/>
                    </div>

                    <!--last name-->
                    <div class="container">
                        <div class="head-row">
                            <img class="icon" src="{{asset('images/icons/user-group-solid-full (1).svg')}}" alt="icon">
                            <label for="last_name">Last Name</label>
                        </div>
                      <x-text-input type="text" name="last_name" id="last_name" placeholder="Enter Last Name" value="{{ Auth::user()->last_name ?? '' }}"/>
                      </div>
                  </div>

                  <!--Phone number-->

                  <div class="second-section">
                     <!--Phone Number-->
                    <div class="container">
                        <div class="head-row">
                            <img class="icon" src="{{asset('images/icons/phone-solid-full.svg')}}" alt="icon">
                            <label for="phone_number">Phone Number</label>
                        </div>
                      <x-text-input type="text" name="phone_number" id="phone_number" placeholder="Enter phone number" value="{{ Auth::user()->phone_number ?? '' }}"/>
                      </div>


                      <!--Email-->
                    <div class="container">
                      <div class="head-row">
                          <img class="icon" src="{{asset('images/icons/calendar-days-solid-full (1).svg')}}" alt="icon">
                          <label for="Email">Email</label>
                      </div>
                      <x-text-input type="email" name="Email" id="Email" placeholder="Enter your Email" value="{{Auth::user()->email ?? ''}}"/>
                    </div>
                </div>

                  <button class="order-car" type="submit">Continue to payment</button>


                  </form>
              </div>

            </div>

  </x-app-layout>

