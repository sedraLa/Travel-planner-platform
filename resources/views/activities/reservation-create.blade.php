<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/ordercar.css') }}">
    @endpush

<div class="main-wrapper">

    <!-- HERO -->
    <div class="hero-background activity-bg">
        <div class="headings">
            <h1>Book Your <span>Activity</span></h1>
            <p>{{ $activity->name }} in {{ $activity->destination->name }}</p>
        </div>
    </div>

    <div class="main-container">

        {{-- Errors --}}
        @if ($errors->any())
            <div class="mb-4 px-4 py-3 bg-red-100 text-red-800 rounded">
                @foreach ($errors->all() as $error)
                    <div>• {{ $error }}</div>
                @endforeach
            </div>
        @endif

        {{-- Success --}}
        @if (session('success'))
            <div class="mb-4 px-4 py-3 bg-green-100 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="form-header">
            <h2>Reserve your activity</h2>
            <p>Fill in details to complete your booking</p>
        </div>

        <div class="form-container">
            <form action="{{ route('activity.reservations.store') }}" method="POST">
                @csrf
        <div class="form-grid">
                <!-- Activity Info -->
                <div class="container">
                    <label>Price per person</label>
                    <input type="text" value="{{ $activity->price }} $" disabled>
                </div>

                <!-- Guests -->
                <div class="container">
                    <label>Number of Guests</label>
                    <input type="number" name="participants_count" min="1" required>
                </div>

                <!-- Date -->
                <div class="container">
                    <label>Activity Date</label>
                    <input type="date" name="activity_date" required>
                </div>

                <!-- User Info -->
                <div class="container">
                    <label>First Name</label>
                    <input type="text" name="name" value="{{ Auth::user()->name }}" required>
                </div>

                <div class="container">
                    <label>Last Name</label>
                    <input type="text" name="last_name" value="{{ Auth::user()->last_name }}" required>
                </div>

                <div class="container">
                    <label>Country</label>
                    <input type="text" name="country" value="{{ Auth::user()->country }}" required>
                </div>

                <div class="container">
                    <label>Email</label>
                    <input type="email" name="email" value="{{ Auth::user()->email }}" required>
                </div>

                <div class="container">
                    <label>Phone Number</label>
                    <input type="text" name="phone_number" value="{{ Auth::user()->phone_number }}" required>
                </div>
                </div>

                <button class="order-car activity" type="submit">
                    Confirm Reservation
                </button>

            </form>
        </div>

    </div>
</div>




<style>
.hero-background.activity-bg {
  background-image: url('/images/trip2.jpg');
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  height: 450px;
  width: 100vw;
  z-index: -1;
  position: absolute;

}


.order-car.activity {
  margin-top: 30px;
  display: block;
  margin-left: auto;
  margin-right: auto;
}



.form-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 20px;
}

.form-grid .full-width {
  grid-column: span 2;
}
</style>    
</x-app-layout>