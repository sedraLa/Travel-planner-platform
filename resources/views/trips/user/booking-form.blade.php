<x-app-layout>
    @push('styles')
    <link rel="stylesheet" href="{{asset("css/ordercar.css")}}">
    @endpush
  <div class="main-wrapper">
          <div class="hero-background">
            <div class="headings">
              <h1>Book Your <span>Perfect Trip</span></h1>
              <p>A life of new experiences is waiting for you</p>
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
                  @if (session('success'))
                  <div class="mb-4 px-4 py-3 bg-green-100 text-green-800 rounded">
                      {{ session('success') }}
                  </div>
              @endif


                  <h2>Only few steps and you are all set</h2>
                  <p>Fill in your personal details</p>
              </div>
              <div class="form-container">
                <form method="POST" action="{{ route('trip.booking.store') }}">
                  @csrf
                  <input type="hidden" name="package_id" value="{{ $package->id }}">
                  @if($availableSchedules->isEmpty())
                    <div class="mb-4 px-4 py-3 bg-red-100 text-red-800 rounded">
                        Booking is closed for this trip.
                    </div>
                  @endif
                  <div class="first-section">
                    <div class="container">
                      <div class="head-row">
                        <img class="icon" src="{{asset('images/icons/calendar-days-solid-full (1).svg')}}" alt="icon">
                          <label for="trip-schedule">Choose Schedule</label>
                      </div>
                      <select name="schedule_id" class="  w-80 border rounded px-3 py-2 border-gray-300" @disabled($availableSchedules->isEmpty())>
                        @foreach($availableSchedules as $s)
                            <option value="{{ $s->id }}">
                                {{ $s->start_date }} - {{ $s->end_date }}
                            </option>
                        @endforeach
                    </select>
                    </div>

                    <div class="container ">
                      <div class="head-row">
                        <img class="icon" src="{{asset('images/icons/user-group-solid-full (1).svg')}}" alt="icon">
                          <label for="people_count">People Count</label>
                      </div>
                      <x-text-input type="number" name="people_count" id="people_count" min="1" required placeholder=" 2 people " :disabled="$availableSchedules->isEmpty()"/>
                    </div>
                  </div>

                  <div class="first-section">
                    <div class="container">
                      <div class="head-row">
                        <img class="icon" src="{{asset('images/icons/user-tie-solid-full.svg')}}" alt="icon">
                        <label for="First Name">First Name</label>
                      </div>
                      <x-text-input type="text" name="name" id="First Name" value="{{ Auth::user()->name ?? '' }}"/>
                    </div>

                    <div class="container ">
                      <div class="head-row">
                        <img class="icon" src="{{asset('images/icons/user-tie-solid-full.svg')}}" alt="icon">
                        <label for="last_name">Last Name</label>
                      </div>
                      <x-text-input type="text" name="last_name" id="last_name" value="{{ Auth::user()->last_name ?? '' }}"/>
                    </div>
                  </div>

                  <div class="first-section">
                    <div class="container">
                      <div class="head-row">
                        <img class="icon" src="{{asset('images/icons/phone-solid-full.svg')}}" alt="icon">
                        <label for="phone_number">Phone Number</label>
                      </div>
                      <x-text-input type="text" name="phone_number" id="phone_number" value="{{ Auth::user()->phone_number ?? '' }}"/>
                    </div>

                    <div class="container ">
                      <div class="head-row">
                        <img class="icon" src="{{asset('images/icons/icons8-email-50.png')}}" alt="icon">
                        <label for="email">Email</label>
                      </div>
                      <x-text-input type="email" name="Email" id="Email" value="{{Auth::user()->email ?? ''}}"/>
                    </div>
                  </div>


                </div>
                  <button class="order-car" style="display: flex,justify-content:center,width:200px;margin:10px auto" type="submit" @disabled($availableSchedules->isEmpty())>Pay With Paypal</button>


              </form>
            </div>

           </div>

  </x-app-layout>
