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
                <div class="form-header">
                    <h2>Contact Information</h2>
                </div>
                <div class="form-container">
                    <form action="{{ route('vehicleReservation.store', $reservation) }}" method="POST">
                        @csrf
                    
                        <input type="hidden" name="pickup_location" value="{{ $reservation->pickup_location }}">
                    <input type="hidden" name="dropoff_location" value="{{ $reservation->dropoff_location }}">
                    <input type="hidden" name="pickup_datetime" value="{{ optional($reservation->pickup_datetime)->format('Y-m-d H:i:s') }}">
                    <input type="hidden" name="passengers" value="{{ $reservation->passengers }}">
                    <input type="hidden" name="total_price" value="{{ $reservation->total_price }}">

                    <div class="first-section">
                        <div class="container">
                            <div class="head-row"><label for="First Name">First Name</label></div>
                            <x-text-input type="text" name="name" id="First Name" value="{{ Auth::user()->name ?? '' }}"/>
                        </div>
                        
                        <div class="container">
                            <div class="head-row"><label for="last_name">Last Name</label></div>
                            <x-text-input type="text" name="last_name" id="last_name" value="{{ Auth::user()->last_name ?? '' }}"/>
                        </div>

                     
                    </div>
                    <div class="second-section">
                        <div class="container">
                            <div class="head-row"><label for="phone_number">Phone Number</label></div>
                            <x-text-input type="text" name="phone_number" id="phone_number" value="{{ Auth::user()->phone_number ?? '' }}"/>
                        </div>

                        <div class="container">
                            <div class="head-row"><label for="email">Email</label></div>
                            <x-text-input type="email" name="Email" id="Email" value="{{Auth::user()->email ?? ''}}"/>
                        </div>
                    </div>

                    <button class="order-car" type="submit">Continue to payment</button>
                </form>
            </div>

            </div>
          </div>
  </x-app-layout>

